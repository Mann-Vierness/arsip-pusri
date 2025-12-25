<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Arsip Dokumen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color:#001f3f; color:#fff; }
    .card { background-color:#003366; color:#fff; border:none; }
    .form-control, .btn { border-radius:1rem; }
  </style>
</head>
<body class="d-flex align-items-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="text-center mb-3">
          <span class="badge text-bg-primary p-3 fs-6 rounded-pill">Arsip PT Pupuk Sriwidjaja</span>
        </div>
        <div class="card shadow-lg rounded-4">
          <div class="card-body p-4">
            <h4 class="mb-3 text-center">Login</h4>

            @if(session('error'))
              <div class="alert alert-danger rounded-4" role="alert">
                {{ session('error') }}
              </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success rounded-4" role="alert">
                {{ session('success') }}
              </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
              @csrf
              <div class="mb-3">
                <label for="badge" class="form-label">Nomor Badge</label>
                <input type="text" class="form-control @error('badge') is-invalid @enderror" 
                       id="badge" name="badge" placeholder="Mis. 123456" 
                       value="{{ old('badge') }}" required autofocus>
                @error('badge')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-warning w-100 fw-semibold">Masuk</button>
            </form>

            <div class="mt-3 text-center">
              <span class="badge text-bg-secondary">Gunakan nomor badge yang didaftarkan admin</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
