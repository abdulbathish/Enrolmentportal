<?php
include_once './constants.php';
require_once './connection.php';
require_once './helpers/user-session.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$loggedInUser = getLoggedInUser($db_conn);
$pageTitle = "";
$cookieDetailsFound = $loggedInUser !== null;

if ($cookieDetailsFound) {
  $buttonClass = "relative inline-flex items-center gap-x-1.5 rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white  shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600";
  $buttonText = "Logout";
  $redirectTo = "logout.php";
} else {
  $buttonClass = "relative inline-flex items-center gap-x-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white  shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600";
  $buttonText = "Login";
  $redirectTo = "login.php";
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/esignet.css">

</head>

<body class="h-full">
  <div class="bg-white">
    <!-- Header -->
    <nav class="bg-black rounded-lg shadow dark:bg-gray-900 m-4 mb-0">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <div class="-ml-2 mr-2 flex items-center md:hidden">
              <!-- Mobile menu button -->
              <button type="button"
                class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
                aria-controls="mobile-menu" aria-expanded="false">
                <span class="absolute -inset-0.5"></span>
                <span class="sr-only">Open main menu</span>
                <!--
              Icon when menu is closed.

              Menu open: "hidden", Menu closed: "block"
            -->
                <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                  aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <!--
              Icon when menu is open.

              Menu open: "block", Menu closed: "hidden"
            -->
                <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                  aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="flex flex-shrink-0 items-center">
              <img class="h-8 w-auto" src="pictures/logo.png" alt="MOSIP">
            </div>
            <div class="hidden md:ml-6 md:flex md:space-x-8">
              <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-white hover:border-gray-300 hover:text-gray-700" -->
              <!-- <a href="#" class="inline-flex items-center border-b-2 border-indigo-500 px-1 pt-1 text-sm font-medium text-gray-900">Home</a> -->
              <a href="index.php"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-white  hover:border-gray-100 hover:text-gray-500">Home</a>
              <a href="https://docs.esignet.io/"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-white hover:border-gray-100 hover:text-gray-500">eSignet</a>
              <a href="https://mosip.io"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-white hover:border-gray-100 hover:text-gray-500">MOSIP</a>
              <?php
              if ($cookieDetailsFound) {
                echo '<a href="dashboard.php" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-white hover:border-gray-100 hover:text-gray-500">Profile</a>';
              } ?>

            </div>
          </div>
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <form action="<?php echo $redirectTo; ?>" method="get">
                <button type="submit" class="<?php echo $buttonClass; ?>">
                  <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="login">
                    <g data-name="Layer 2">
                      <path
                        d="M19 4h-2a1 1 0 0 0 0 2h1v12h-1a1 1 0 0 0 0 2h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1zm-7.2 3.4a1 1 0 0 0-1.6 1.2L12 11H4a1 1 0 0 0 0 2h8.09l-1.72 2.44a1 1 0 0 0 .24 1.4 1 1 0 0 0 .58.18 1 1 0 0 0 .81-.42l2.82-4a1 1 0 0 0 0-1.18z"
                        data-name="log-in"></path>
                    </g>
                  </svg>

                  <?php echo $buttonText; ?>
                </button>
              </form>
            </div>
            <div class="hidden md:ml-4 md:flex md:flex-shrink-0 md:items-center">

              <!-- Profile dropdown -->
              <div class="relative ml-3">
                <div>
                  <!-- <button type="button" class="relative flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="absolute -inset-1.5"></span>
                <span class="sr-only">Open user menu</span>
                <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
              </button> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile menu, show/hide based on menu state. -->
      <div class="md:hidden" id="mobile-menu">
        <div class="space-y-1 pb-3 pt-2">
          <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-white hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
          <!-- <a href="#" class="block border-l-4 border-indigo-500 bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700 sm:pl-5 sm:pr-6">Dashboard</a> -->
          <a href="#"
            class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-white hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">Home</a>
          <a href="https://docs.esignet.io/"
            class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-white hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">eSignet</a>
          <a href="https://mosip.io"
            class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-white hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">MOSIP</a>
          <?php
          if ($cookieDetailsFound) {
            echo '<a href="user.php" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-white hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">Profile</a>';
          } ?>

        </div>
      </div>
    </nav>
    <script>
      document.getElementById('redirectButton').addEventListener('click', function () {
        window.location.href = '<?php echo $redirectUrl; ?>';
      });
    </script>