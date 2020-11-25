var roof = null;
var roofPoints = [];
var lines = [];
var lineCounter = 0;
var drawingObject = {};
drawingObject.type = "";
drawingObject.background = "";
drawingObject.border = "";

function Point(x, y) {
    this.x = x;
    this.y = y;
}

$("#deleteObj").click(function () {
    var activeObj = canvas.getActiveObject();
    if (confirm('Biztosan szeretnéd törölni?')) {
        canvas.remove(activeObj);
    }

    canvas.renderAll();
});



$("#poly").click(function () {
    if (drawingObject.type === "roof") {
        drawingObject.type = "";
        lines.forEach(function(value, index, ar){
            canvas.remove(value);
        });
        roof = makeRoof(roofPoints);
        canvas.add(roof);
        canvas.renderAll();
    } else {
        drawingObject.type = "roof"; // roof type
    }
});

$("#getRoute").click(function () {
    if (isCanvasBlank(canvas)) {
        alert("Először rajzolj valamit kérlek!");
    } else {
        var poly = canvas.getObjects()[0];
        poly.set("fill", "white");
        poly.set("stroke", "white");
        poly.set("strokeWidth", 4);
        canvas.backgroundColor = "black";

        $("#imgEditToMap").val(canvas.toDataURL());

        $("#mapForm").submit();
    }
})

$("#fileupload").change(function () {
    var file = $("input[name='gpxFile[]']")[0];
    if (file.files.length == 1) {
        $('#fileuploadtext').text(file.files.item(0).name);
    } else if(file.files.length > 1) {
        $('#fileuploadtext').text(file.files.length + ' file selected');
    }
})


$("#fromStrava").click(function () {
    if (isCanvasBlank(canvas)) {
        alert("Először rajzolj valamit kérlek!")
    } else {
        var dataURLorig = canvas.toDataURL();

        var poly = canvas.getObjects()[0];
        poly.set("fill", "white");
        poly.set("stroke", "white");
        poly.set("strokeWidth", 4);
        canvas.backgroundColor = "black";

        var dataURLedited = canvas.toDataURL();

        poly.set("fill", "white");
        poly.set("stroke", "blue");
        poly.set("strokeWidth", 3);
        canvas.backgroundColor = "white";

        $("#imgOrigstrava").val(dataURLorig);
        $("#imgEditstrava").val(dataURLedited);

        $("#strava").submit();
    }

})


$("#upload").click(function () {
    //var file = document.getElementsByName("gpxFile");
    var file = $("input[name='gpxFile[]']")[0];
    if (isCanvasBlank(canvas)) {
        alert("Először rajzolj valamit kérlek!");
    } else if (file.files.length == 0) {
        alert("Kérlek válaszd ki a fájlt a feltöltéshez!");
    } else {
        var filesGood = true;
        for(var i = 0; i < file.files.length; i++) {
            if (!file.files.item(i).name.endsWith(".gpx")){
                filesGood = false;
                alert("Csak GPX kiterjesztésű fájl feltöltésére van lehetőség!");
            }
        }

        if (filesGood) {
            var dataURLorig = canvas.toDataURL();

            var poly = canvas.getObjects()[0];
            poly.set("fill", "white");
            poly.set("stroke", "white");
            poly.set("strokeWidth", 4);
            canvas.backgroundColor = "black";

            var dataURLedited = canvas.toDataURL();

            poly.set("fill", "white");
            poly.set("stroke", "blue");
            poly.set("strokeWidth", 3);
            canvas.backgroundColor = "white";

            $("#imgOrig").val(dataURLorig);
            $("#imgEdit").val(dataURLedited);

            $("#form").submit();
        }
    }
});

function isCanvasBlank(canvas) {
    return !canvas.getContext('2d')
        .getImageData(0, 0, canvas.width, canvas.height).data
        .some(channel => channel !== 0);
}

// canvas Drawing
var canvas = new fabric.Canvas('canvas');
var x = 0;
var y = 0;

fabric.util.addListener(window,'dblclick', function(){
    drawingObject.type = "";
    lines.forEach(function(value, index, ar){
        canvas.remove(value);
    });
    //canvas.remove(lines[lineCounter - 1]);
    roof = makeRoof(roofPoints);
    canvas.add(roof);
    canvas.renderAll();

    console.log("double click");
    //clear arrays
    roofPoints = [];
    lines = [];
    lineCounter = 0;

});

canvas.on('mouse:down', function (options) {
    if (drawingObject.type === "roof") {
        canvas.selection = false;
        setStartingPoint(options); // set x,y
        roofPoints.push(new Point(x, y));
        var points = [x, y, x, y];
        lines.push(new fabric.Line(points, {
            strokeWidth: 3,
            selectable: false,
            stroke: 'blue'
        }));
        canvas.add(lines[lineCounter]);
        lineCounter++;
        canvas.on('mouse:up', function (options) {
            canvas.selection = true;
        });
    }
    console.log("mouse down");
});
canvas.on('mouse:move', function (options) {
    if (lines[0] !== null && lines[0] !== undefined && drawingObject.type === "roof") {
        setStartingPoint(options);
        lines[lineCounter - 1].set({
            x2: x,
            y2: y
        });
        canvas.renderAll();
    }
});

function setStartingPoint(options) {
    var offset = $('#canvas').offset();
    x = options.e.pageX - offset.left;
    y = options.e.pageY - offset.top;
}

function makeRoof(roofPoints) {
    var left = findLeftPaddingForRoof(roofPoints);
    var top = findTopPaddingForRoof(roofPoints);
    var temp1 = roofPoints[0].x;
    roofPoints.push(new Point(temp1,roofPoints[0].y))
    var roof = new fabric.Polyline(roofPoints, {
        fill: 'rgba(255,255,255,1)',
        stroke:'#000FFF',
        strokeWidth: 3
    });
    roof.set({

        left: left,
        top: top,

    });


    return roof;
}

function findTopPaddingForRoof(roofPoints) {
    var result = 999999;
    for (var f = 0; f < lineCounter; f++) {
        if (roofPoints[f].y < result) {
            result = roofPoints[f].y;
        }
    }
    return Math.abs(result);
}

function findLeftPaddingForRoof(roofPoints) {
    var result = 999999;
    for (var i = 0; i < lineCounter; i++) {
        if (roofPoints[i].x < result) {
            result = roofPoints[i].x;
        }
    }
    return Math.abs(result);
}