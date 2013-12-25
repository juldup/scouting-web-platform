<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
    @yield('title')
  </title>
</head>
<body>
  @yield('header')
  @include('menu')
	@yield('content')
  @yield('footer')
</body>
</html>
