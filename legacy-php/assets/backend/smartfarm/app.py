import time
import threading
import random
import json
from flask import Flask, jsonify, request, send_from_directory
from flask_cors import CORS
from datetime import datetime

app = Flask(__name__)
CORS(app)

GLOBAL_STATE = {
    "sensors": { 
        "ph": 6.0, "humidity": 80, "temperature": 25.0, "ec": 2.0, "light": 7000, "tds": 1000,
        "water_level": 80.0 
    },
    "controls": {
        "return_pump_on": False, "mister_pump_on": False, "uv_light_on": False,
        "ultrasonic_mister_on": False, "exhaust_fan_on": False, "mist_fan_on": False, "grow_light_on": False,
        "ai_mode_enabled": False
    },
    "targets": { "manual": { "ph": 6.0, "humidity": 80, "temperature": 25, "ec": 1.5, "light": 7000, "tds": 750 }, "program": {} },
    "growth_program": [],
    "cultivation": { "status": "idle", "start_timestamp": None, "pause_timestamp": None, "accumulated_time": 0 },
    "logs": []
}

def log_system_event(level, message):
    timestamp = datetime.now().strftime('%d/%m/%Y %H:%M:%S')
    GLOBAL_STATE["logs"].append({"timestamp": timestamp, "level": level, "message": message})
    if len(GLOBAL_STATE["logs"]) > 100: GLOBAL_STATE["logs"] = GLOBAL_STATE["logs"][-100:]

def simulation_loop():
    while True:
        try:
            sensors = GLOBAL_STATE["sensors"]
            sensors["ph"] = round(max(0, min(14, sensors["ph"] + random.uniform(-0.1, 0.1))), 1)
            sensors["temperature"] = round(max(0, min(50, sensors["temperature"] + random.uniform(-0.2, 0.2))), 1)
            sensors["humidity"] = int(max(0, min(100, sensors["humidity"] + random.uniform(-1, 1))))
            sensors["light"] = int(max(0, sensors["light"] + random.uniform(-50, 50)))
            if sensors["tds"] > 100: sensors["tds"] = int(sensors["tds"] - 1)
            if sensors["ec"] > 0.1: sensors["ec"] = round(max(0, sensors["ec"] - 0.002), 3)
            if sensors["water_level"] > 0: sensors["water_level"] = round(max(0, sensors["water_level"] - 0.1), 1)

            controls = GLOBAL_STATE["controls"]
            targets = GLOBAL_STATE["targets"]["manual"]
            if controls["ai_mode_enabled"]:
                if sensors["temperature"] > targets["temperature"] + 1.0: controls["exhaust_fan_on"] = True
                elif sensors["temperature"] < targets["temperature"] - 0.5: controls["exhaust_fan_on"] = False
                if sensors["humidity"] < targets["humidity"] - 5: controls["mister_pump_on"] = True; controls["ultrasonic_mister_on"] = True; controls["mist_fan_on"] = True
                elif sensors["humidity"] >= targets["humidity"]: controls["mister_pump_on"] = False; controls["ultrasonic_mister_on"] = False; controls["mist_fan_on"] = False
                if sensors["light"] < 5000: controls["grow_light_on"] = True
                else: controls["grow_light_on"] = False
                controls["return_pump_on"] = (int(time.time()) % 60 < 30)
                controls["uv_light_on"] = (int(time.time()) % 60 < 30)

            if random.random() < 0.001: log_system_event("WARNING", "High Temperature Detected (Simulated)")
            if random.random() < 0.0005: log_system_event("ERROR", "Sensor Read Timeout: EC Sensor")
            time.sleep(2)
        except Exception as e:
            print(f"Error: {e}")
            time.sleep(5)

threading.Thread(target=simulation_loop, daemon=True).start()

@app.route('/')
def index(): return send_from_directory('.', 'index.html')

@app.route('/get_latest_data', methods=['GET'])
def get_latest_data():
    cult = GLOBAL_STATE["cultivation"]
    start_t = None
    elapsed = 0
    if cult["status"] == "running": start_t = cult["start_timestamp"] * 1000
    elif cult["status"] == "paused": elapsed = cult["accumulated_time"]
    return jsonify({"status": "success", "timestamp": time.time(), "sensors": GLOBAL_STATE["sensors"], "controls": GLOBAL_STATE["controls"], "cultivation_status": cult["status"], "start_time": start_t, "elapsed_time": elapsed, "logs": GLOBAL_STATE["logs"]})

@app.route('/start_cultivation', methods=['POST'])
def start_cultivation():
    cult = GLOBAL_STATE["cultivation"]
    now = time.time()
    if cult["status"] == "paused":
        cult["start_timestamp"] += (now - cult["pause_timestamp"])
        cult["status"] = "running"; cult["pause_timestamp"] = None
        log_system_event("INFO", "Cultivation Resumed")
    elif cult["status"] == "idle":
        cult["start_timestamp"] = now; cult["accumulated_time"] = 0; cult["status"] = "running"
        log_system_event("INFO", "Cultivation Started")
    return jsonify({"status": "success", "start_time": cult["start_timestamp"] * 1000})

@app.route('/stop_cultivation', methods=['POST'])
def stop_cultivation():
    cult = GLOBAL_STATE["cultivation"]
    if cult["status"] == "running":
        now = time.time(); cult["pause_timestamp"] = now; cult["accumulated_time"] = now - cult["start_timestamp"]; cult["status"] = "paused"
        log_system_event("INFO", "Cultivation Paused")
    return jsonify({"status": "success"})

@app.route('/reset_cultivation', methods=['POST'])
def reset_cultivation():
    cult = GLOBAL_STATE["cultivation"]
    cult["status"] = "idle"; cult["start_timestamp"] = None; cult["pause_timestamp"] = None; cult["accumulated_time"] = 0
    log_system_event("WARNING", "Cultivation Reset")
    return jsonify({"status": "success"})

@app.route('/update_controls', methods=['POST'])
def update_controls():
    d = request.json
    if 'manual_targets' in d: GLOBAL_STATE["targets"]["manual"].update(d['manual_targets'])
    if 'ai_mode_enabled' in d: GLOBAL_STATE["controls"]["ai_mode_enabled"] = bool(d['ai_mode_enabled'])
    return jsonify({"status": "success"})

@app.route('/save_program', methods=['POST'])
def save_program():
    log_system_event("INFO", "Growth Program Saved")
    return jsonify({"status": "success"})

if __name__ == '__main__':
    log_system_event("INFO", "Server Started")
    app.run(debug=True, port=5000, use_reloader=False)