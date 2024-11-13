<?php
$page_title = "Home";

$nav_home_class = "active_page";
?>
<!DOCTYPE html>
<html lang="en">

<?php include("includes/meta.php"); ?>

<body>
  <?php include("includes/header.php"); ?>

  <main>
    <h2>INFO 2300</h2>

    <?php if (is_user_logged_in()) { ?>
      <p>Welcome <strong><?php echo htmlspecialchars($current_user["name"]); ?></strong>!</p>
    <?php } ?>

    <p>This website is rendered server-side in PHP.</p>

    <!-- Note: Avoid outputting your PHP version in your production HTML.         -->
    <!--       Malicious actors may use the version to try and hack your website. -->
    <p>You're running PHP version: <strong><?php echo phpversion(); ?></strong>.</p>

    <?php if (!is_user_logged_in()) { ?>
      <h2>Sign In</h2>

      <?php echo login_form("/", $session_messages); ?>

    <?php } ?>

    <h2>Testing</h2>

    <p>Test your <a href="/a/page/does/not/exist/at/this/url">404 page</a>.
  </main>

  <?php include("includes/footer.php"); ?>
</body>

</html>
