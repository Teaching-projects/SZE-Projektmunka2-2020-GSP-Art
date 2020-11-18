import osmnx as ox
import networkx as nx
import numpy as np
import cv2
import os, sys

G = ox.graph_from_point(center_point=(47.68835, 17.63674), dist=1000, network_type='walk')
CYCLES = nx.cycle_basis(nx.Graph(G))
BEST_IDX = 1500

def generate():
    global CYCLES
    # reszgrafok mentese fajlba
    for i in range(len(CYCLES)):
        ox.plot_graph(G.subgraph(CYCLES[i]), figsize=(5.2, 5.2), show=False, close=True,
                      bgcolor="black", edge_color="w",
                      node_size=3, edge_linewidth=2,
                      save=True, filepath="mapIMGs/{}.png".format(i))

    # kepek transzformalasa
    for i in range(len(CYCLES)):
        img = cv2.imread('mapIMGs/{}.png'.format(i))
        cv2.imwrite('mapIMGs/{}.png'.format(i), fill_poly(img))


def resize_image(img, size, interpolation):
    h, w = img.shape[:2]
    c = None if len(img.shape) < 3 else img.shape[2]
    if h == w: return cv2.resize(img, (size, size), interpolation)
    if h > w: dif = h
    else:     dif = w
    x_pos = int((dif - w)/2.)
    y_pos = int((dif - h)/2.)
    if c is None:
        mask = np.zeros((dif, dif), dtype=img.dtype)
        mask[y_pos:y_pos+h, x_pos:x_pos+w] = img[:h, :w]
    else:
        mask = np.zeros((dif, dif, c), dtype=img.dtype)
        mask[y_pos:y_pos+h, x_pos:x_pos+w, :] = img[:h, :w, :]
    return cv2.resize(mask, (size, size), interpolation)


#kepeken korvonal fillelese, kepek atmeretezese es felulirasa
def fill_poly(img):
    imgray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    ret, thresh = cv2.threshold(imgray, 127, 255, 0)

    contours, hierarchy = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)

    cv2.fillPoly(img,pts=contours,color=(255,255,255))
    img = resize_image(img, 500, cv2.INTER_AREA)
    return img


def search_in_graph(filename):
    global CYCLES
    global BEST_IDX
    searched = cv2.imread('images/edit/tmp/' + filename, cv2.IMREAD_GRAYSCALE)

    images = []
    scores = []

    best_dist = 20
    best_idx = 1500

    for i in range(len(CYCLES)):
        img = cv2.imread('mapIMGs/{}.png'.format(i), cv2.IMREAD_GRAYSCALE)
        images.append(img)
        d = cv2.matchShapes(searched, img, cv2.CONTOURS_MATCH_I2, 0)
        if d < best_dist:
            best_dist = d
            best_idx = i
        scores.append((1 - d) * 100)
    BEST_IDX = best_idx


def get_lon_lat():
    global CYCLES
    global BEST_IDX
    global G

    lines = node_list_to_path(G.subgraph(CYCLES[BEST_IDX]), CYCLES[BEST_IDX])
    lon2 = []
    lat2 = []
    for i in range(len(lines)):
        z = list(lines[i])
        l1 = list(list(zip(*z))[0])
        l2 = list(list(zip(*z))[1])
        for j in range(len(l1)):
            lon2.append(l1[j])
            lat2.append(l2[j])
    return lon2, lat2


def node_list_to_path(g, node_list):
    edge_nodes = list(zip(node_list[:-1], node_list[1:]))
    lines = []
    for u, v in edge_nodes:
        data = min(g.get_edge_data(u, v).values(),
                   key=lambda x: x['length'])
        if 'geometry' in data:
            xs, ys = data['geometry'].xy
            lines.append(list(zip(xs, ys)))
        else:
            x1 = g.nodes[u]['x']
            y1 = g.nodes[u]['y']
            x2 = g.nodes[v]['x']
            y2 = g.nodes[v]['y']
            line = [(x1, y1), (x2, y2)]
            lines.append(line)
    return lines


def main():
    filename = sys.argv[1]
    if len(os.listdir("mapIMGs")) == 0:
        generate()

    search_in_graph(filename)

    lon, lat = get_lon_lat()

    lon.append(lon[0])
    lat.append(lat[0])

    print(str(lon) + "//" + str(lat))



if __name__ == '__main__':
    main()