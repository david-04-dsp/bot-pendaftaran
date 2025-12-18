/*
Huffman coding simulation in C
- Input: a text string (default: "MAMA SAYA") or user-provided via stdin
- Output: character frequencies, Huffman codes for each character, encoded bitstring, and basic stats

Image reference (uploaded by user): /mnt/data/decf11fc-e10e-4cf3-a051-737225903c27.png

Compile: gcc huffman_simulation.c -o huffman
Run:     ./huffman

Author: Generated for user (simulation of the example on the image)
*/

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define MAX_CHARS 256

// Huffman tree node
typedef struct Node {
    unsigned char ch;      // character (if leaf)
    unsigned freq;         // frequency
    struct Node *left, *right;
} Node;

// Min-heap for nodes
typedef struct MinHeap {
    unsigned size;
    unsigned capacity;
    Node array;
} MinHeap;

Node* newNode(unsigned char ch, unsigned freq) {
    Node* temp = (Node*)malloc(sizeof(Node));
    temp->ch = ch;
    temp->freq = freq;
    temp->left = temp->right = NULL;
    return temp;
}

MinHeap* createMinHeap(unsigned capacity) {
    MinHeap* minHeap = (MinHeap*)malloc(sizeof(MinHeap));
    minHeap->size = 0;
    minHeap->capacity = capacity;
    minHeap->array = (Node)malloc(minHeap->capacity * sizeof(Node*));
    return minHeap;
}

void swapNode(Node** a, Node** b) {
    Node* t = *a;
    *a = *b;
    *b = t;
}

void minHeapify(MinHeap* minHeap, int idx) {
    int smallest = idx;
    int left = 2*idx + 1;
    int right = 2*idx + 2;

    if (left < (int)minHeap->size && minHeap->array[left]->freq < minHeap->array[smallest]->freq)
        smallest = left;
    if (right < (int)minHeap->size && minHeap->array[right]->freq < minHeap->array[smallest]->freq)
        smallest = right;
    if (smallest != idx) {
        swapNode(&minHeap->array[smallest], &minHeap->array[idx]);
        minHeapify(minHeap, smallest);
    }
}

int isSizeOne(MinHeap* minHeap) {
    return (minHeap->size == 1);
}

Node* extractMin(MinHeap* minHeap) {
    Node* temp = minHeap->array[0];
    minHeap->array[0] = minHeap->array[minHeap->size - 1];
    --minHeap->size;
    minHeapify(minHeap, 0);
    return temp;
}

void insertMinHeap(MinHeap* minHeap, Node* node) {
    ++minHeap->size;
    int i = minHeap->size - 1;
    while (i && node->freq < minHeap->array[(i - 1) / 2]->freq) {
        minHeap->array[i] = minHeap->array[(i - 1) / 2];
        i = (i - 1) / 2;
    }
    minHeap->array[i] = node;
}

void buildMinHeap(MinHeap* minHeap) {
    int n = minHeap->size - 1;
    for (int i = (n - 1) / 2; i >= 0; --i)
        minHeapify(minHeap, i);
}

int isLeaf(Node* node) {
    return !(node->left) && !(node->right);
}

MinHeap* createAndBuildMinHeap(unsigned freq[], unsigned char chars[], int uniqueCount) {
    MinHeap* minHeap = createMinHeap(uniqueCount);
    for (int i = 0; i < uniqueCount; ++i)
        minHeap->array[i] = newNode(chars[i], freq[chars[i]]);
    minHeap->size = uniqueCount;
    buildMinHeap(minHeap);
    return minHeap;
}

Node* buildHuffmanTree(unsigned freq[], unsigned char chars[], int uniqueCount) {
    Node *left, *right, *top;
    MinHeap* minHeap = createAndBuildMinHeap((unsigned*)freq, chars, uniqueCount);

    while (!isSizeOne(minHeap)) {
        left = extractMin(minHeap);
        right = extractMin(minHeap);

        top = newNode('$', left->freq + right->freq);
        top->left = left;
        top->right = right;

        insertMinHeap(minHeap, top);
    }
    Node* root = extractMin(minHeap);
    free(minHeap->array);
    free(minHeap);
    return root;
}

// store codes as strings (dynamically allocated)
char* codes[MAX_CHARS];

void printCodesRecursive(Node* root, char* arr, int top) {
    if (root->left) {
        arr[top] = '0';
        printCodesRecursive(root->left, arr, top + 1);
    }
    if (root->right) {
        arr[top] = '1';
        printCodesRecursive(root->right, arr, top + 1);
    }
    if (isLeaf(root)) {
        arr[top] = '\0';
        // copy into codes
        codes[root->ch] = (char*)malloc((top + 1) * sizeof(char));
        strcpy(codes[root->ch], arr);
    }
}
void buildCodes(Node* root) {
    char arr[256];
    for (int i = 0; i < MAX_CHARS; ++i) codes[i] = NULL;
    printCodesRecursive(root, arr, 0);
}

void freeTree(Node* root) {
    if (!root) return;
    freeTree(root->left);
    freeTree(root->right);
    free(root);
}

int main() {
    char input[1024];
    printf("Masukkan teks (tekan enter untuk default 'MAMA SAYA'): \n");
    if (!fgets(input, sizeof(input), stdin)) return 0;
    // remove newline
    size_t len = strlen(input);
    if (len > 0 && input[len-1] == '\n') input[len-1] = '\0';
    if (strlen(input) == 0) strcpy(input, "MAMA SAYA");

    unsigned freq[MAX_CHARS] = {0};
    int uniqueCount = 0;
    int total = 0;

    for (size_t i = 0; i < strlen(input); ++i) {
        unsigned char c = (unsigned char)input[i];
        if (freq[c] == 0) uniqueCount++;
        freq[c]++;
        total++;
    }

    if (uniqueCount == 0) {
        printf("Tidak ada karakter untuk diproses.\n");
        return 0;
    }

    // collect unique characters into array
    unsigned char chars[256];
    int idx = 0;
    for (int i = 0; i < MAX_CHARS; ++i) {
        if (freq[i] > 0) {
            chars[idx++] = (unsigned char)i;
        }
    }

    Node* root = buildHuffmanTree(freq, chars, idx);
    buildCodes(root);

    printf("\n=== Frekuensi karakter ===\n");
    for (int i = 0; i < idx; ++i) {
        unsigned char c = chars[i];
        if (c == ' ') printf("' ' (spasi): %u\n", freq[c]);
        else printf("'%c': %u\n", c, freq[c]);
    }

    printf("\n=== Kode Huffman ===\n");
    for (int i = 0; i < idx; ++i) {
        unsigned char c = chars[i];
        if (c == ' ') printf("' ' (spasi): %s\n", codes[c]);
        else printf("'%c': %s\n", c, codes[c]);
    }

    // encode input
    printf("\n=== Encode teks ===\n");
    size_t outCap = 1024;
    char* encoded = (char*)malloc(outCap);
    encoded[0] = '\0';
    size_t outLen = 0;

    for (size_t i = 0; i < strlen(input); ++i) {
        unsigned char c = (unsigned char)input[i];
        char* code = codes[c];
        if (!code) continue; // shouldn't happen
        size_t need = strlen(code);
        if (outLen + need + 1 > outCap) {
            outCap *= 2;
            encoded = (char*)realloc(encoded, outCap);
        }
        strcat(encoded, code);
        outLen += need;
    }

    printf("Teks asli: %s\n", input);
    printf("Encoded bits: %s\n", encoded);
    printf("Total karakter: %d\n", total);
    printf("Total bits (encoded): %zu\n", outLen);
    printf("Rata-rata bit per simbol: %.3f\n", (double)outLen / total);

    // cleanup
    for (int i = 0; i < MAX_CHARS; ++i) if (codes[i]) free(codes[i]);
    free(encoded);
    freeTree(root);

    return 0;
}