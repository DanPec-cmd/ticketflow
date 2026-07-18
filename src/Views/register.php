<!DOCTYPE html>
<html lang="hr">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">Registracija</h1>
        <form action="/register/submit" method="POST">
            <input type="text" name="name" placeholder="Ime i prezime" required class="w-full p-2 border mb-3">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border mb-3">
            <input type="password" name="password" placeholder="Lozinka" required class="w-full p-2 border mb-4">
            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded">Registriraj se</button>
        </form>
        <p class="mt-4 text-sm text-center">Već imate račun? <a href="/login" class="text-blue-600 underline">Prijavite se</a></p>
    </div>
</body>
</html>