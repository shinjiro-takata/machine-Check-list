<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7f8">
    <title>@yield('title', '設備点検') | Fieldnote</title>
    {{-- 共通スタイルと画面の動作は、専用のアセットファイルから読み込む。 --}}
    <link rel="stylesheet" href="{{ asset('css/equipment-check.css') }}">
    <script src="{{ asset('js/equipment-check.js') }}" defer></script>
</head>
<body>
{{-- アプリケーション共通のヘッダー。 --}}
<header class="topbar">
    <a class="brand" href="{{ route('inspections.create') }}">
        <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M5 12.5 9.2 17 19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 3.5 19.5 6v5.7c0 4.1-3.1 7.7-7.5 9.3-4.4-1.6-7.5-5.2-7.5-9.3V6L12 3.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></span>
        <span class="brand-name">設備点検</span><span class="brand-caption">Fieldnote</span>
    </a>
    <nav class="topbar-right" aria-label="メインメニュー">
        <a class="nav-link" href="{{ route('inspections.create') }}">点検入力</a>
        <a class="nav-link" href="{{ route('inspections.index') }}">点検履歴</a>
    </nav>
</header>
{{-- 点検入力、履歴、記録詳細の各画面をここに表示する。 --}}
<main>
    @if (session('status'))<div class="flash" role="status">{{ session('status') }}</div>@endif
    @yield('content')
</main>
</body>
</html>
