<?php
/*******************************************************
 * Project:     ggg-cc-portfolio
 * File:        browse.php
 * Author:      Your name
 * Date:        2020-06-15
 * Version:     1.0.0
 * Description:
 *******************************************************/
?>
<!DOCTYPE HTML>
<html lang="en-AU">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>App Practice | Category | Browse</title>

    <!-- CSS required -->
    <!-- Bootstrap 4.x -->
    <link rel="stylesheet" href="/app/assets/bs/css/bootstrap.min.css">
    <!-- FontAwesome 5.x -->
    <link rel="stylesheet" href="/app/assets/fa/css/all.min.css">

</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="../">Demo APP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="../">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="../product" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Product  <span class="sr-only">(current)</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../product/browse.php">Browse</a>
                    <a class="dropdown-item" href="../product/create.php">Add</a>
                </div>
            </li>

            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="../category" id="navbarDropdown"
                   role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Category
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../category/browse.php">Browse</a>
                    <a class="dropdown-item" href="../category/create.php">Add</a>
                </div>
            </li>
    </div>
</nav>

<!-- container -->
<main role="main" class="container">

    <div class="row">
        <div class="col-sm">
            <h1>Browse Categories</h1>
        </div>
    </div>

    <?php
    // include Database connection
    include '../../config/Database.php';
    // include the utilities class
    include '../../classes/Utils.php';

    $database = new Database();
    $conn = $database->getConnection();

    // select all data
    $query = "SELECT id, code, name, description FROM categories ORDER BY id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    // number of rows returned
    $totalRecords = $stmt->rowCount();
    //$num = $totalRecords;

    // set the page records to 5
    $pageRecords = 5;
    $displayPages = (int)ceil($totalRecords/$pageRecords);

    $activePage = 1;

    // If user request a page number greater than $displayPages, set $activePage to the maximum pages number
    if (isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] >0 ){
        if ((int)$_GET['page'] > $displayPages){
            $activePage = $displayPages;
        } else {$activePage = (int)$_GET['page']; }
    }

    // exclude records depending on which page the user is
    $skipRecords = ($activePage - 1) * $pageRecords;


    if ($pageRecords > 0 && $skipRecords >= 0) {

        $query2 = "SELECT id, code, name, description FROM categories ORDER BY id DESC LIMIT :skipRecords, :pageRecords";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':skipRecords', $skipRecords, PDO::PARAM_INT);
        $stmt2->bindParam(':pageRecords', $pageRecords, PDO::PARAM_INT);
    } else{
        $query2 = "SELECT id, code, name, description FROM categories ORDER BY id DESC";
        $stmt2 = $conn->prepare($query2);
    }

    $stmt2->execute();

    $num = $stmt2->rowCount();


    // check if more than 0 records found
    if ($num >0) {
    ?>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $stmt2->fetch(PDO::FETCH_OBJ)) {
            // create new table row per record
        ?>
        <tr>
            <td><?= $row->id ?></td>
            <td><?= $row->code ?></td>
            <td><?= $row->name ?></td>
            <td><?= $row->description ?></td>
            <td>
                <a href="../category/read.php?id=<?=$row->id ?>"
                   class="btn btn-info mr-1">
                    Read
                </a>

                <a href="../category/update.php?id=<?=$row->id ?>"
                   class="btn btn-info mr-1">
                    Edit
                </a>

                <a href="../category/delete.php?id=<?= $row->id ?>"
                   class="btn btn-danger">
                    Delete
                </a>
            </td>
        </tr>
        <?php
            }
        ?>
        </tbody>
    </table>

    <?php
    } else {
        // if no records found
        $messages[] = ['info'=>'No Records found'];
        Utils::messages($messages);
    }
    ?>

    <div class="row">
        <div class="col-sm">
            <p>Page content in here</p>
        </div>

    </div>

    <div class="row">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?= $activePage <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= "browse.php?page=".($activePage - 1 < 1 ? $activePage : $activePage - 1) ?>">Previous</a></li>

                <?php
                for($i = 0; $i < $displayPages; $i++){?>
                    <li class="<?= ($activePage === $i+1) ? "page-item active" : "page-item" ?>">
                        <a class="page-link" href="<?= "browse.php?page=".($i+1)?>"><?= $i+1 ?></a>
                    </li>
                <?php
                }
                ?>

                <li class="page-item <?= $activePage >= $displayPages ? 'disabled' : ''?>">
                    <a class="page-link" href="<?= "browse.php?page=".($displayPages) ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>



</main> <!-- end .container -->

<!-- JavaScript that is required -->
<script src="/app/assets/jquery/jquery-3.5.1.min.js"></script>
<script src="/app/assets/popper/popper.min.js"></script>
<script src="/app/assets/bs/js/bootstrap.min.js"></script>

</body>
