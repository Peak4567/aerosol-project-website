// Global State Object
const state = {
    isConnected: false,
    startTime: null,
    cultivationStatus: 'idle', // idle, running, paused

    // Sensor Data Cache
    currentTDS: 0,
    currentEC: 0,
    currentWater: 0,

    // Calculation History
    history: [], // {time, tds, ec}
    waterHistory: [], // {time, value}

    // Timers & Logs
    cultivationTimer: null,
    logs: [],

    // Config
    growthProgram: []
};