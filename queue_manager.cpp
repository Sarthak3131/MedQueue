#include <iostream>
#include <fstream>
#include <vector>
#include <ctime>
#include <sstream>

using namespace std;

#define MAX 5

struct Patient {
    int id;
    int token;
    string doctor;
    string department;
    string created_at;
};

vector<Patient> queue;

const string OUTPUT_FILE = "C:\\xampp\\htdocs\\MedQueue\\output.txt";

void saveQueueToFile() {
    ofstream out(OUTPUT_FILE);
    for (auto &p : queue) {
        out << p.id << "|" << p.token << "|" << p.doctor << "|" << p.department << "|" << p.created_at << "\n";
    }
    out.close();
}

string getCurrentTimestamp() {
    time_t now = time(0);
    tm *ltm = localtime(&now);
    char buf[30];
    strftime(buf, sizeof(buf), "%Y-%m-%d %H:%M:%S", ltm);
    return string(buf);
}

void addPatient(int id, string doctor, string department, int token) {
    if (queue.size() >= MAX) {
        cout << "Queue Full\n";
        return;
    }

    Patient p = {id, token, doctor, department, getCurrentTimestamp()};
    queue.push_back(p);
    saveQueueToFile();
}

void removePatient(int id) {
    for (auto it = queue.begin(); it != queue.end(); ++it) {
        if (it->id == id) {
            queue.erase(it);
            break;
        }
    }
    saveQueueToFile();
}

void loadQueueFromFile() {
    queue.clear();
    ifstream in(OUTPUT_FILE);
    string line;
    while (getline(in, line)) {
        if (line.empty()) continue;
        stringstream ss(line);
        Patient p;
        string token;

        getline(ss, token, '|');
        p.id = stoi(token);

        getline(ss, token, '|');
        p.token = stoi(token);

        getline(ss, p.doctor, '|');
        getline(ss, p.department, '|');
        getline(ss, p.created_at);

        queue.push_back(p);
    }
    in.close();
}

int main(int argc, char* argv[]) {
    if (argc < 2) {
        cout << "Usage: queue_manager <add/remove> ...\n";
        return 1;
    }

    string cmd = argv[1];
    loadQueueFromFile();

    if (cmd == "add" && argc == 6) {
        int id = stoi(argv[2]);
        string doctor = argv[3];
        string department = argv[4];
        int token = stoi(argv[5]);
        addPatient(id, doctor, department, token);
    } else if (cmd == "remove" && argc == 3) {
        int id = stoi(argv[2]);
        removePatient(id);
    } else {
        cout << "Invalid command\n";
    }

    return 0;
}
