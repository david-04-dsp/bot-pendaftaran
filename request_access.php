<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Access - Pendaftaran Mahasiswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 60px;
            margin-bottom: 10px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .info-box {
            background: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }

        .info-box h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        .help-text {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .steps {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 25px;
        }

        .steps h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .step {
            display: flex;
            align-items: start;
            margin-bottom: 12px;
            font-size: 14px;
            color: #555;
        }

        .step-number {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            font-weight: bold;
            font-size: 12px;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }

        .message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }

        .telegram-link {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f0f7ff;
            border-radius: 8px;
        }

        .telegram-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
        }

        .telegram-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <div class="logo-icon">🎓</div>
            <h2>Pendaftaran Mahasiswa</h2>
            <p class="subtitle">Sistem Passwordless Authentication</p>
        </div>

        <div class="info-box">
            <h3>🔐 Akses Tanpa Password</h3>
            <p>Tidak perlu mengingat password! Kami akan mengirimkan link akses khusus ke Telegram Anda.</p>
            <p style="margin-top: 10px;"><strong>💡 Tip:</strong> Lebih mudah langsung chat dengan bot di Telegram!</p>
        </div>

        <div id="message" class="message"></div>

        <form id="requestForm" method="POST">
            <div class="form-group">
                <label for="contact">Telegram Username / Nama Anda</label>
                <input type="text" id="contact" name="contact" placeholder="@username atau David" required>
                <p class="help-text">Masukkan username Telegram Anda (dengan @) atau nama lengkap Anda di Telegram</p>
            </div>

            <button type="submit" id="submitBtn">
                🚀 Minta Link Akses
            </button>
        </form>

        <div class="steps">
            <h3>📋 Cara Kerja:</h3>
            <div class="step">
                <div class="step-number">1</div>
                <div>Masukkan username Telegram atau nama Anda di Telegram (pastikan sudah pernah chat dengan bot)</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div>Sistem akan mengirim link khusus ke Telegram Anda</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div>Klik link tersebut untuk mengakses form pendaftaran</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div>Isi form dan selesai! Tidak perlu password</div>
            </div>
        </div>

        <div class="telegram-link">
            <p style="margin-bottom: 8px; color: #666; font-size: 14px;">Belum punya akses ke bot?</p>
            <a href="https://t.me/percobaan_pendaftaran_bot" target="_blank">
                📱 Buka Bot Telegram
            </a>
        </div>
    </div>

    <script>
        const form = document.getElementById('requestForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageDiv = document.getElementById('message');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const contact = document.getElementById('contact').value.trim();
            
            if (!contact) {
                showMessage('Mohon masukkan username Telegram atau nomor telepon', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Mengirim permintaan...';

            try {
                const response = await fetch('generate_magic_link.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `contact=${encodeURIComponent(contact)}`
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(
                        '✅ Link akses berhasil dikirim! Silakan cek Telegram Anda dan klik link yang dikirimkan oleh bot.',
                        'success'
                    );
                    form.reset();
                } else {
                    showMessage('❌ ' + (data.message || 'Gagal mengirim link. Pastikan Anda sudah chat dengan bot terlebih dahulu.'), 'error');
                }
            } catch (error) {
                showMessage('❌ Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = '🚀 Minta Link Akses';
            }
        });

        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 10000);
            }
        }
    </script>
</body>
</html>
