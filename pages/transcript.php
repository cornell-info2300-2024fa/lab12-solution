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

$page_title = "Transcript";

$nav_transcript_class = "active_page";

if (is_user_logged_in()) {
  include_once("includes/transcript-values.php");

  // CSS classes for sort arrows
  $sort_css_classes = array(
    "course_asc" => "inactive",
    "course_desc" => "inactive",
    "term_asc" => "inactive",
    "term_desc" => "inactive",
    "year_asc" => "inactive",
    "year_desc" => "inactive",
    "credits_asc" => "inactive",
    "credits_desc" => "inactive",
    "grade_asc" => "inactive",
    "grade_desc" => "inactive",
  );

  // URL query string for NEXT sort
  $sort_next_url = array(
    "course" => "course",
    "term" => "term",
    "year" => "year",
    "credits" => "credits",
    "grade" => "grade"
  );

  // URL query string for NEXT order
  $order_next_url = array(
    "course" => "asc",
    "term" => "asc",
    "year" => "asc",
    "credits" => "asc",
    "grade" => "asc"
  );

  // retrieve query string parameters for sorting
  $sort_param = $_GET["sort"] ?? NULL;
  $order_param = $_GET["order"] ?? NULL;

  // SQL query parts
  $sql_select_clause = "SELECT
  grades.id AS 'grades.id',
  courses.number AS 'courses.number',
  courses.credits AS 'courses.credits',
  grades.term AS 'grades.term',
  grades.acad_year AS 'grades.acad_year',
  grades.grade AS 'grades.grade'
FROM grades INNER JOIN courses ON (grades.course_id = courses.id)";

  $sql_order_clause = ""; // no default order

  // validate sort's order parameter
  // order must be: asc or desc
  if ($order_param == "asc") {
    // ascending
    $sql_sort_order = "ASC";

    $order_next = "desc";
    $filter_icon = "up";
  } else if ($order_param == "desc") {
    // descending
    $sql_sort_order = "DESC";

    $order_next = NULL;
    $filter_icon = "down";
  } else {
    // no order
    $sql_sort_order = NULL;

    $order_next = "asc";
    $filter_icon = NULL;
  }

  // validate order parameter.
  // sort must be "course", "term", "year", "credits", or "grade"
  if ($sql_sort_order && in_array($sort_param, array("course", "term", "year", "credits", "grade"))) {

    // rotate URLS through sort asc, sort desc, sort none
    if ($order_next == NULL) {
      $sort_next_url[$sort_param] = NULL;
    }
    $order_next_url[$sort_param] = $order_next;

    // Table sorter icon should match current sort
    if ($filter_icon == "up") {
      $sort_css_classes[$sort_param . "_asc"] = "";
      $sort_css_classes[$sort_param . "_desc"] = "hidden";
    } else if ($filter_icon == "down") {
      $sort_css_classes[$sort_param . "_asc"] = "hidden";
      $sort_css_classes[$sort_param . "_desc"] = "";
    }

    // SQL sort by field
    // map query string values to database fields
    $sql_sort_fields = array(
      "course" => "courses.number",
      "term" => "grades.term",
      "year" => "grades.acad_year",
      "credits" => "courses.credits",
      "grade" => "grades.grade"
    );
    $sql_sort_field = $sql_sort_fields[$sort_param];

    // order by SQL clause
    $sql_order_clause = " ORDER BY " . $sql_sort_field . " " . $sql_sort_order;
  } else {
    // sort params are invalid
    $sort_param = NULL;
    $order_param = NULL;
  }

  // build the final query
  // glue the select clause to the order clause
  $sql_select_query = $sql_select_clause . $sql_order_clause . ";";

  // query grades table
  $records = exec_sql_query($db, $sql_select_query)->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include "includes/meta.php" ?>

<body>
  <?php include "includes/header.php" ?>

  <main class="transcript">
    <h2><?php echo $page_title; ?></h2>

    <?php if (is_user_logged_in()): ?>

      <!-- Note: You may not copy the instructor's code/design and submit it  -->
      <!--       as your own.                                                 -->
      <!--                                                                    -->
      <!-- We studied textual catalog design patterns in class, there are     -->
      <!-- many design alternatives for for presenting textual information.   -->
      <!-- You may use this design for inspiration. However, you must design  -->
      <!-- your own.                                                          -->
      <!--                                                                    -->
      <!-- Remember, design is a learning objective of this class. Your your  -->
      <!-- future employers will expect you to  be able to design on your own -->
      <!-- without copying someone else's work. Use this experience as        -->
      <!-- practice.                                                          -->

      <p><strong>Note:</strong> No credit is provided for submitting design and/or code that is taken from course-provided examples.Do not copy this design and/or code into your project submission and then change it.</p>

      <table>
        <tr>
          <th class="column-course">
            <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                                "sort" => $sort_next_url["course"],
                                                "order" => $order_next_url["course"]
                                              )) ?>" aria-label="Sort by Course Number">
              Course
              <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-38.257 -61.073)">
                  <path class="sort_desc <?php echo $sort_css_classes["course_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                  <path class="sort_asc <?php echo $sort_css_classes["course_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
                </g>
              </svg>
            </a>
          </th>

          <th class="column-term">
            <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                                "sort" => $sort_next_url["term"],
                                                "order" => $order_next_url["term"]
                                              )) ?>" aria-label="Sort by Term">
              Term
              <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-38.257 -61.073)">
                  <path class="sort_desc <?php echo $sort_css_classes["term_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                  <path class="sort_asc <?php echo $sort_css_classes["term_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
                </g>
              </svg>
            </a>
          </th>

          <th class="column-year">
            <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                                "sort" => $sort_next_url["year"],
                                                "order" => $order_next_url["year"]
                                              )) ?>" aria-label="Sort by Academic Year">
              Year
              <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-38.257 -61.073)">
                  <path class="sort_desc <?php echo $sort_css_classes["year_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                  <path class="sort_asc <?php echo $sort_css_classes["year_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
                </g>
              </svg>
            </a>
          </th>

          <th class="column-credits">
            <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                                "sort" => $sort_next_url["credits"],
                                                "order" => $order_next_url["credits"]
                                              )) ?>" aria-label="Sort by Academic Credits">
              Credits
              <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-38.257 -61.073)">
                  <path class="sort_desc <?php echo $sort_css_classes["credits_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                  <path class="sort_asc <?php echo $sort_css_classes["credits_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
                </g>
              </svg>
            </a>
          </th>

          <th class="column-grade min">
            <a class="sort" href="/transcript?<?php echo http_build_query(array(
                                                "sort" => $sort_next_url["grade"],
                                                "order" => $order_next_url["grade"]
                                              )) ?>" aria-label="Sort by Grade">
              Grade
              <svg class="icon" version="1.1" viewBox="0 0 2.1391 4.2339" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(-38.257 -61.073)">
                  <path class="sort_desc <?php echo $sort_css_classes["grade_desc"]; ?>" d="m40.396 63.455-1.0695 1.8521-1.0695-1.8521z" />
                  <path class="sort_asc <?php echo $sort_css_classes["grade_asc"]; ?>" d="m40.396 62.925h-2.1391l1.0695-1.8521z" />
                </g>
              </svg>
            </a>
          </th>
        </tr>

        <?php
        // write a table row for each record
        foreach ($records as $record) {
          $course = $record["courses.number"];
          $term = TERM_CODINGS[$record["grades.term"]];
          $year = ACADEMIC_YEAR_CODINGS[$record["grades.acad_year"]];
          $credits = $record["courses.credits"];
          $grade = $record["grades.grade"] ?? "";

          // row partial
          include "includes/transcript-record.php";
        } ?>

      </table>

    <?php else: ?>
      <p>Please login to view this resource.</p>

      <h3>Sign In</h3>

      <?php echo login_form("/transcript", $session_messages); ?>
    <?php endif; ?>

  </main>

  <?php include "includes/footer.php" ?>
</body>

</html>
