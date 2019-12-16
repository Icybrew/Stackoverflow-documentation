<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <title>{{ title }}</title>
</head>
<body>
<div class="container">
    <!-- Beggining of the Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">StackOverflow-CRUD</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Table selection
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">table 1</a>
                        <a class="dropdown-item" href="#">table 2</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">table 3 if needed</a>
                    </div>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>
    <!-- Table field for the content-->
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Collum</th>
            <th scope="col">Collum</th>
            <th scope="col">Collum</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th scope="row">1</th>
            <td>Example</td>
            <td>Example</td>
            <td>Example</td>
        </tr>
        <tr>
            <th scope="row">2</th>
            <td>Example</td>
            <td>Example</td>
            <td>Example</td>
        </tr>
        <tr>
            <th scope="row">3</th>
            <td>Example</td>
            <td>Example</td>
            <td>Example</td>
        </tr>
        <tr><td></td><td></td><td></td><td></td></tr> <!-- A gap after the last table's record-->
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer navbar-fixed-bottom">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit" id="create">Create</button>
    </div>
</div>
</body>
</html>
