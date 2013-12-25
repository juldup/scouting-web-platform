<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
    @yield('title')
  </title>
</head>
<body>
  @include('menu.header')
  @include('menu.menu')
  @include('menu.tabs')
	@yield('content')
  @include('menu.footer')
</body>
</html>
