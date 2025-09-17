<?php include '../config/app.php'; ?>
<!doctype html>
<html lang='en'>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pawnshop Management System</title>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/datatables.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/style.css">

<!-- Select2 CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
<link rel="stylesheet" href="../assets/css/select2.min.css">
<link rel="icon" href="../assets/img/pawnshop icon.png" type="image/x-icon">


</head>
<style>
    /* customized style for stack cards */
    .summary-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .card-icon {
        font-size: 2rem;
        margin-bottom: 8px;
    }

    .card-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
</style>

<!-- <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
    <div class='container-fluid'>
        <a class='navbar-brand' href='dashboard.php'>Pawnshop</a>
        <div class='collapse navbar-collapse'>
            <ul class='navbar-nav'>
                <li class='nav-item'><a class='nav-link' href='dashboard.php'>Dashboard</a></li>
                <li class='nav-item'><a class='nav-link' href='pawns.php'>Pawned Items</a></li>
                <li class='nav-item'><a class='nav-link' href='claims.php'>Claims</a></li>
                <li class='nav-item'><a class='nav-link' href='remata.php'>Remata</a></li>
                <li class='nav-item'><a class='nav-link' href='reports.php'>Reports</a></li>
            </ul>
        </div>
    </div>
</nav> -->
