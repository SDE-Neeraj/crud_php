<?php
session_start();
include "connection.php";

$insert = false;
$delete = false;
$update = false;

if (isset($_GET['delete'])) {
  $sno = $_GET['delete'];
  $sql_delete = "DELETE FROM `notes` WHERE `sno` = $sno";
  $result = mysqli_query($conn, $sql_delete);

  if ($result) {
    $_SESSION['delete'] = true;
    header("Location: /crud_php/index.php");
    exit();
  } else {
    echo "Error deleting note.";
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['snoEdit'])) {
    $title = $_POST["editTitle"];
    $sno = $_POST["snoEdit"];
    $description = $_POST["editDescription"];

    $sql_update = "UPDATE `notes` SET `title` = '$title' , `description` = '$description' WHERE `notes`.`sno` = $sno";
    $result = mysqli_query($conn, $sql_update);

    if ($result) {
      $_SESSION['update'] = true;
      header("Location: /crud_php/index.php");
      exit();
    } else {
      echo "Error updating note.";
    }
  } else {
    $title = $_POST["title"];
    $description = $_POST["description"];

    $sql_insert = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
    $result = mysqli_query($conn, $sql_insert);

    if ($result) {
      $_SESSION['insert'] = true;
      header("Location: /crud_php/index.php");
      exit();
    } else {
      echo "Error inserting note.";
    }
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Language" content="en">
  <title>Crud Using PHP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

</head>

<body>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editModalLabel">Modal title</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/crud_php/index.php" method="POST">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="mb-3">
              <label for="editTitle" class="form-label">Note Title</label>
              <input type="text" class="form-control" name="editTitle" id="editTitle" aria-describedby="emailHelp">
            </div>
            <div class="form-group">
              <label for="editDescription">Note Description</label>
              <textarea class="form-control mt-2" rows="3" name="editDescription" id="editDescription"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">PHP CRUD</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
        </ul>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>

  <!-- Success/Failure Messages -->
  <?php
  if (isset($_SESSION['insert'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
      <strong>Success!</strong> Your note has been added successfully.
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
    unset($_SESSION['insert']);
  } elseif (isset($_SESSION['delete'])) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
      <strong>Success!</strong> The note has been deleted successfully.
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
    unset($_SESSION['delete']);
  } elseif (isset($_SESSION['update'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
      <strong>Success!</strong> The note has been updated successfully.
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
    unset($_SESSION['update']);
  }
  ?>


  <div class="container my-5">
    <h2>Add a Note</h2>
    <form action="/crud_php/index.php" method="POST">
      <div class="mb-3">
        <label for="title" class="form-label">Note Title</label>
        <input type="text" class="form-control" name="title" id="title" aria-describedby="emailHelp">
      </div>
      <div class="form-group">
        <label for="description">Note Description</label>
        <textarea class="form-control mt-2" rows="3" name="description" id="description"></textarea>
      </div>
      <button type="submit" class="btn btn-primary mt-3">Add Note</button>
    </form>
  </div>

  <div class="container my-5">
    <table class="table" id='myTable'>
      <thead>
        <tr>
          <th scope="col">Serial No.</th>
          <th scope="col">Title</th>
          <th scope="col">Description</th>
          <th scope="col">Created On</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM `notes`";
        $result = mysqli_query($conn, $sql);
        $sno = 0;
        while ($row = mysqli_fetch_assoc($result)) {
          $sno = $sno + 1;
          echo "<tr>
          <th scope='row'>" . $sno . "</th>
          <td>" . $row['title'] . "</td>
          <td>" . $row['description'] . "</td>
          <td>" . $row['tstamp'] . "</td>
          <td> <button class='edit btn btn-sm btn-primary' id='$row[sno]'>Edit</button> <button class='delete btn btn-sm btn-primary' id='$row[sno]'>Delete</button></td>
        </tr>";
        }
        ?>
      </tbody>
      <!-- Button Edit modal -->
      <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
  Launch demo modal
</button> -->
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
  <script>
    $(document).ready(function () {
      // Initialize DataTable
      let table = $('#myTable').DataTable();

      // Delegate edit button click to table for dynamic rows
      $('#myTable').on('click', '.edit', function (e) {
        let tr = $(this).closest('tr');
        let title = tr.find('td').eq(0).text();
        let description = tr.find('td').eq(1).text();
        $('#editTitle').val(title);
        $('#editDescription').val(description);
        $('#snoEdit').val($(this).attr('id'));
        $('#editModal').modal('toggle');
      });

      // Delegate delete button click to table for dynamic rows
      $('#myTable').on('click', '.delete', function (e) {
        let sno = $(this).attr('id');
        if (confirm('Are you sure you want to delete this note?')) {
          window.location = `/crud_php/index.php?delete=${sno}`;
        }
      });
    });
  </script>
</body>

</html>