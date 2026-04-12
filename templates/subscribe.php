<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Subscribe to GitHub Releases</title>
  <style>
    /* Базовый набор Tailwind утилит для автономности формы */
    body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; background-color: #f9fafb; color: #111827; }
    .container { width: 100%; max-width: 28rem; margin: 4rem auto; padding: 2rem; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    p { color: #4b5563; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1rem; }
    label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem; }
    input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; transition: border-color 0.15s ease; }
    input:focus { outline: 2px solid transparent; outline-offset: 2px; border-color: #2563eb; ring: 3px rgba(37, 99, 235, 0.5); }
    button { width: 100%; background-color: #2563eb; color: #ffffff; font-weight: 600; padding: 0.625rem; border-radius: 0.375rem; transition: background-color 0.15s ease; cursor: pointer; border: none; }
    button:hover { background-color: #1d4ed8; }
    .msg { margin-top: 1rem; padding: 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; display: none; }
    .msg.ok { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; display: block; }
    .msg.err { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; display: block; }
    code { background-color: #f3f4f6; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    small { font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem; display: block; }
    .footer { margin-top: 1.5rem; text-align: center; }
  </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  <div class="container bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h1 class="text-2xl font-bold mb-2">Підписка на релізи GitHub</h1>
    <p class="text-gray-600 mb-6 text-sm">Вкажіть email та репозиторій у форматі <code>owner/repo</code>, щоб отримувати сповіщення про нові релізи.</p>

    <form id="subscribe-form" class="space-y-4">
      <div class="form-group">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" name="email" type="email" placeholder="you@example.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
      </div>
      <div class="form-group">
        <label for="repo" class="block text-sm font-medium text-gray-700">Репозиторій</label>
        <input id="repo" name="repo" type="text" placeholder="owner/repo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
        <small class="text-xs text-gray-500 mt-1">Приклад: torvalds/linux</small>
      </div>
      <div>
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Підписатися
        </button>
      </div>
    </form>

    <div id="message" class="msg"></div>

    <div class="footer">
      <small>
        Сервіс надається «як є». Відписатися можна за посиланням із листа.
      </small>
    </div>
  </div>

  <script>
    const form = document.getElementById('subscribe-form');
    const message = document.getElementById('message');

    function showMessage(text, ok = true) {
      message.textContent = text;
      message.className = 'msg ' + (ok ? 'ok' : 'err');
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const payload = {
        email: form.email.value.trim(),
        repo: form.repo.value.trim(),
      };
      try {
        const res = await fetch('/api/subscribe', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok) {
          showMessage('Перевірте вашу пошту: надіслано лист із підтвердженням.');
          form.reset();
        } else {
          let msg = 'Помилка при підписці';
          if (data && data.error && data.error.message) {
            msg = data.error.message;
            if (data.error.code) {
              msg += ` (Код: ${data.error.code})`;
            }
          }
          showMessage(msg, false);
        }
      } catch (err) {
        showMessage('Мережа недоступна або сервер не відповідає', false);
      }
    });
  </script>
</body>
</html>
