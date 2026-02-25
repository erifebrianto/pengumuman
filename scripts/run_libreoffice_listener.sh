#!/bin/bash

# ==========================================================
# Script untuk menjalankan LibreOffice UNO listener 
# sebagai background process.
# ==========================================================

# Port default UNO listener
PORT=2002

echo "Memeriksa proses LibreOffice di port $PORT..."
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null ; then
    echo "LibreOffice UNO Listener sudah berjalan."
else
    echo "Memulai LibreOffice UNO Listener di background..."
    # Menjalankan libreoffice secara headless
    libreoffice --headless --nologo --nofirststartwizard --accept="socket,host=127.0.0.1,port=$PORT;urp;" &
    
    # Alternatif jika menggunakan unoconv, bisa cukup:
    # unoconv --listener &
    
    echo "LibreOffice UNO Listener berhasil dijalankan."
fi
