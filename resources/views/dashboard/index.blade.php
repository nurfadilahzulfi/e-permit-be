<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin E-Permit</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="font-bold text-lg">Dashboard Admin E-Permit</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</button>
        </form>
    </nav>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ url('user') }}" class="bg-white p-4 rounded shadow hover:shadow-md">Manage User</a>
        <a href="{{ url('permit-types') }}" class="bg-white p-4 rounded shadow hover:shadow-md">Permit Types</a>
        <a href="{{ url('permit-gwp') }}" class="bg-white p-4 rounded shadow hover:shadow-md">Permit GWP</a>
        <a href="{{ url('permit-gwp-approval') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP Approvals</a>
        <a href="{{ url('permit-gwp-completion') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP Completion</a>
        <a href="{{ url('gwp-cek') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP Checklist</a>
        <a href="{{ url('gwp-cek-pemohon-ls') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP Pemohon LS</a>
        <a href="{{ url('gwp-cek-hse-ls') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP HSE LS</a>
        <a href="{{ url('gwp-alat-ls') }}" class="bg-white p-4 rounded shadow hover:shadow-md">GWP Alat LS</a>
    </div>
</body>
</html>
