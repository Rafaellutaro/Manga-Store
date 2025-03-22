function showToast(message, type = "default") {
    let bgColor;

    switch (type) {
        case "success":
            bgColor = "linear-gradient(to right, #4CAF50, #66BB6A)"; // Green
            break;
        case "error":
            bgColor = "linear-gradient(to right, #FF5733, #C70039)"; // Red
            break;
        case "warning":
            bgColor = "linear-gradient(to right, #FFA500, #FF8C00)"; // Orange
            break;
        case "info":
            bgColor = "linear-gradient(to right, #2196F3, #64B5F6)"; // Blue
            break;
        default:
            bgColor = "linear-gradient(to right, #333, #666)"; // Gray (Default)
    }

    Toastify({
        text: message,
        duration: 3000,
        gravity: "top", 
        position: 'center',
        backgroundColor: bgColor,
        close: true, // Adds a close button
    }).showToast();
}

