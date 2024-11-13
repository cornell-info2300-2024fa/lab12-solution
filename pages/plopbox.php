<?php
/* Note: No credit is provided for submitting design and/or code that is     */
/*       taken from course-provided examples.                                */
/*                                                                           */
/* Do not copy this code into your project submission and then change it.    */
/*                                                                           */
/* Write your own code from scratch. Use this example as a REFERENCE only.   */
/*                                                                           */
/* You may not copy this code, change a few names/variables, and then claim  */
/* it as your own.                                                           */
/*                                                                           */
/* Examples are provided to help you learn. Copying the example and then     */
/* changing it a bit, does not help you learn the learning objectives of     */
/* this assignment. You need to write your own code from scratch to help you */
/* learn.                                                                    */

$page_title = "Clipart Plop Box";

$nav_plopbox_class = "active_page";

// Access Control - Only logged in users may upload
if (is_user_logged_in()) {

  // Set maximum file size for uploaded files.
  // MAX_FILE_SIZE must be set to bytes
  // 1 MB = 1000000 bytes
  define("MAX_FILE_SIZE", 1000000);

  $upload_feedback = array(
    "general_error" => false,
    "too_large" => false
  );

  // upload fields
  $upload_source = NULL;
  $upload_file_name = NULL;
  $upload_file_ext = NULL;

  // was the upload form submitted?
  if (isset($_POST["upload"])) {

    $upload_source = trim($_POST["source"]);
    if ($upload_source == "") {
      $upload_source = NULL;
    }

    // get the info about the uploaded files.
    $upload = $_FILES["svg-file"];

    // Assume the form is valid...
    $form_valid = true;

    // file is required
    if ($upload["error"] == UPLOAD_ERR_OK) {
      // The upload was successful!

      // Get the name of the uploaded file without any path
      $upload_file_name = basename($upload["name"]);

      // Get the file extension of the uploaded file and convert to lowercase for consistency in DB
      $upload_file_ext = strtolower(pathinfo($upload_file_name, PATHINFO_EXTENSION));

      // This site only accepts SVG files!
      if (!in_array($upload_file_ext, array("svg"))) {
        $form_valid = false;
        $upload_feedback["general_error"] = true;
      }
    } else if (($upload["error"] == UPLOAD_ERR_INI_SIZE) || ($upload["error"] == UPLOAD_ERR_FORM_SIZE)) {
      // file was too big, let's try again
      $form_valid = false;
      $upload_feedback["too_large"] = true;
    } else {
      // upload was not successful
      $form_valid = false;
      $upload_feedback["general_error"] = true;
    }

    if ($form_valid) {
      // insert upload into DB
      $result = exec_sql_query(
        $db,
        "INSERT INTO clipart (file_name, file_ext, source) VALUES (:file_name, :file_ext, :source)",
        array(
          ":file_name" => $upload_file_name,
          ":file_ext" => $upload_file_ext,
          ":source" => $upload_source
        )
      );

      if ($result) {
        // We successfully inserted the record into the database, now we need to
        // move the uploaded file to it's final resting place: public/uploads directory

        // get the newly inserted record's id
        $record_id = $db->lastInsertId("id");

        // uploaded file should be in folder with same name as table with the primary key as the filename.
        // Note: THIS IS NOT A URL; this is a FILE PATH on the server!
        //       Do NOT include / at the beginning of the path; path should be a relative path.
        //          NO: /public/...
        //         YES:  public/...
        $upload_storage_path = "public/uploads/clipart/" . $record_id . "." . $upload_file_ext;

        // Move the file to the public/uploads/clipart folder
        // Note: THIS FUNCTION REQUIRES A PATH. NOT A URL!
        if (move_uploaded_file($upload["tmp_name"], $upload_storage_path) == false) {
          error_log("Failed to permanently store the uploaded file on the file server. Please check that the server folder exists.");
        }
      }
    }
  }
}

// query the database for the clipart records
$records = exec_sql_query(
  $db,
  "SELECT * FROM clipart ORDER BY file_name ASC;",
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<?php include "includes/meta.php" ?>

<body>
  <?php include "includes/header.php"; ?>

  <main class="plopbox">

    <p><strong>Note:</strong> No credit is provided for submitting design and/or code that is taken from course-provided examples.Do not copy this design and/or code into your project submission and then change it.</p>

    <section class="gallery">
      <h2><?php echo $page_title; ?></h2>

      <?php
      // Only show the clipart gallery if we have records to display.
      if (count($records) > 0) { ?>
        <ul>
          <?php
          foreach ($records as $record) {
            $file_url = "/public/uploads/clipart/" . $record["id"] . "." . $record["file_ext"];
          ?>
            <li>
              <a href="<?php echo htmlspecialchars($file_url) ?>" title="Download <?php echo htmlspecialchars($record["file_name"]); ?>" download>
                <div class="thumbnail">
                  <img src="<?php echo htmlspecialchars($file_url); ?>" alt="<?php echo htmlspecialchars($record["file_name"]); ?>">
                  <p><?php echo htmlspecialchars($record["file_name"]); ?></p>
                </div>
                <div class="overlay">
                  <img alt="" src="/public/images/download-icon.svg">
                </div>
              </a>
            </li>
          <?php
          } ?>
        </ul>
      <?php
      } else { ?>
        <p>Your Plop Box clipart collection is <em>empty</em>. Try uploading some clipart.</p>
      <?php } ?>
    </section>

    <?php
    // Access Controls - Interface: Only logged in users may upload
    if (is_user_logged_in()): ?>

      <section class="upload">
        <h2>Upload Clipart</h2>

        <form action="/plopbox" method="post" enctype="multipart/form-data">

          <!-- MAX_FILE_SIZE must precede the file input field -->
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>">

          <?php if ($upload_feedback["too_large"]) { ?>
            <p class="feedback">We're sorry. The file failed to upload because it was too big. Please select a file that&apos;s no larger than 1MB.</p>
          <?php } ?>

          <?php if ($upload_feedback["general_error"]) { ?>
            <p class="feedback">We're sorry. Something went wrong. Please select an SVG file to upload.</p>
          <?php } ?>

          <div class="label-input">
            <label for="upload-file">SVG File:</label>
            <!-- This site only accepts SVG files! -->
            <input id="upload-file" type="file" name="svg-file" accept=".svg,image/svg+xml">
          </div>

          <div class="label-input">
            <label for="upload-source" class="optional">Source URL:</label>
            <input id="upload-source" type="url" name="source" placeholder="URL where found. (optional)">
          </div>

          <div class="align-right">
            <button type="submit" name="upload">Upload Clipart</button>
          </div>

        </form>
      </section>

    <?php else: // user is not logged in. show login form 
    ?>

      <h2>Sign In</h2>

      <p>Please login to upload clipart to Plop Box</p>

      <?php echo login_form('/plopbox#upload', $session_messages); ?>

    <?php endif; ?>

  </main>

  <?php include "includes/footer.php"; ?>
</body>

</html>
