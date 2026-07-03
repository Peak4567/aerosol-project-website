# ==============================================================================
# SECTION 1: IMPORTS & INITIAL SETUP
# ==============================================================================
import time
import random
import logging
import threading
from flask import Flask, render_template
from flask_socketio import SocketIO

# --- Basic Setup ---
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# --- Flask & SocketIO Setup ---
app = Flask(__name__)
# Allow connections from any origin (important for 2-computer setup)
socketio = SocketIO(app, cors_allowed_origins="*") 

SENSOR_CONFIG = {
    "ph": {"name": "PH", "unit": "", "min": 4, "max": 8},
    "humidity": {"name": "ความชื้น", "unit": "%", "min": 50, "max": 95},
    "temperature": {"name": "อุณหภูมิ", "unit": "°C", "min": 15, "max": 30},
    "ec": {"name": "ค่าปุ๋ย (EC)", "unit": "", "min": 1.0, "max": 2.5},
    "light": {"name": "ความเข้มแสง", "unit": " lux", "min": 2000, "max": 12000},
    "shed": {"name": "ความชื้นโรงเรือน", "unit": "%", "min": 60, "max": 95}
}

# ==============================================================================
# SECTION 2: SYSTEM DATA MANAGEMENT CLASS
# ==============================================================================
class SystemManager:
    """ Manages all system data and simulation logic. """
    def __init__(self):
        self.lock = threading.Lock()
        self.reset_system()

    def reset_system(self):
        """ Resets all data for a new session. """
        with self.lock:
            self.day = 0
            self.is_ai_on = True
            self.plants = {
                "Plant_ID_01": {"img": "https://placehold.co/400x225/1a2c1a/ffffff?text=Plant+A", "history": {"leafCount": [10], "rootLength": [5], "tuberCount": [0]}, "events": [{"day": 0, "event": "เริ่มการปลูก"}]}
            }
            self.sensor_data = {}
            self.sensor_targets = {}
            self.sensor_history = {}
            for key, config in SENSOR_CONFIG.items():
                start_val = config['min'] + (config['max'] - config['min']) * 0.6
                self.sensor_data[key] = start_val
                self.sensor_targets[key] = start_val
                self.sensor_history[key] = [start_val]

            self.growth_profiles = [
                {"startDay": 1, "endDay": 10, "targets": {"ph": 6.0, "humidity": 80, "temperature": 24, "ec": 1.5, "light": 7000, "shed": 85}},
                {"startDay": 11, "endDay": 20, "targets": {"ph": 7.0, "humidity": 75, "temperature": 22, "ec": 1.8, "light": 9000, "shed": 80}}
            ]
            self.system_errors = {}

    def apply_auto_profile(self):
        """ Applies target values from the growth profile if AI is active. """
        if not self.is_ai_on:
            return
        active_profile = next((p for p in self.growth_profiles if p['startDay'] <= self.day <= p['endDay']), None)
        if active_profile:
            self.sensor_targets.update(active_profile['targets'])
            
    def check_system_integrity(self):
        """ Checks for sensor anomalies like sudden jumps or stuck values. """
        self.system_errors = {}
        for key, config in SENSOR_CONFIG.items():
            history = self.sensor_history[key]
            if len(history) < 2: continue
            
            jump_threshold = (config['max'] - config['min']) * 0.5
            if abs(history[-1] - history[-2]) > jump_threshold:
                self.system_errors[key] = "ค่าเปลี่ยนแปลงกะทันหัน!"
            
            if self.day > 5 and len(history) > 3 and history[-1] == history[-2] == history[-3]:
                 if random.random() < 0.2:
                    self.system_errors[key] = "เซ็นเซอร์อาจค้าง"

    def simulate_new_day(self):
        """ Simulates the passing of one day, updating sensor data and plant growth. """
        with self.lock:
            self.day += 1
            self.apply_auto_profile()

            # Simulate sensor data fluctuation
            for key, config in SENSOR_CONFIG.items():
                current_val = self.sensor_data[key]
                target_val = self.sensor_targets[key]
                if self.is_ai_on: current_val += (target_val - current_val) * 0.4
                current_val += (random.random() - 0.5) * (config['max'] - config['min']) * 0.05
                current_val = max(config['min'], min(config['max'], current_val))
                self.sensor_data[key] = current_val
                self.sensor_history[key].append(current_val)

            self.check_system_integrity()

    def get_full_state(self):
        """ Gathers all current data into a dictionary to send to the frontend. """
        with self.lock:
            return {
                'day': self.day,
                'is_ai_on': self.is_ai_on,
                'plants': self.plants,
                'sensor_data': self.sensor_data,
                'sensor_targets': self.sensor_targets,
                'sensor_history': self.sensor_history,
                'growth_profiles': self.growth_profiles,
                'system_errors': self.system_errors,
                'sensor_config': SENSOR_CONFIG
            }

# ==============================================================================
# SECTION 3: FLASK & SOCKET.IO ROUTES
# ==============================================================================
manager = SystemManager()

@app.route('/')
def index():
    return render_template('ai.php')

@socketio.on('connect')
def handle_connect():
    """ Handles new web client connections, resetting and sending initial data. """
    logging.info("Client connected. Sending initial data.")
    manager.reset_system() 
    socketio.emit('initial_data', manager.get_full_state())

@socketio.on('toggle_ai')
def handle_toggle_ai(data):
    manager.is_ai_on = data.get('is_on')
    logging.info(f"AI mode set to {'ON' if manager.is_ai_on else 'OFF'}")

@socketio.on('update_target')
def handle_update_target(data):
    with manager.lock:
        key, value = data.get('key'), data.get('value')
        if key in manager.sensor_targets:
            manager.sensor_targets[key] = float(value)
            manager.is_ai_on = False # Turn off AI on manual override
            logging.info(f"Manual target for {key} updated to: {value}")
            socketio.emit('state_update', manager.get_full_state())

@socketio.on('update_profiles')
def handle_update_profiles(data):
    with manager.lock:
        manager.growth_profiles = data.get('profiles', [])
    logging.info("Growth profiles updated from client.")
    socketio.emit('state_update', manager.get_full_state())

def background_thread():
    """ The main background task that simulates data and sends updates. """
    logging.info("Background thread started.")
    while True:
        manager.simulate_new_day()
        socketio.emit('update', manager.get_full_state())
        socketio.sleep(3) # Update interval in seconds

# ==============================================================================
# SECTION 4: MAIN EXECUTION BLOCK
# ==============================================================================
if __name__ == '__main__':
    socketio.start_background_task(background_thread)
    socketio.run(app, host='0.0.0.0', port=5000, debug=False, allow_unsafe_werkzeug=True)

