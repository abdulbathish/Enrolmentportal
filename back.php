 <form method="get" action="<?php echo $actionUrl; ?>">
    <input type="hidden" name="redirect_uri" value="<?php echo CALLBACK_URL; ?>" />
    <input type="hidden" name="client_id" value="<?php echo CLIENT_ID; ?>" />
    <input type="hidden" name="scope" value="<?php echo SCOPE; ?>" />
    <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
    <input type="hidden" name="state" value="<?php echo STATE; ?>" />
    <input type="hidden" name="acr_values" value="<?php echo ACR_VALUES; ?>" />
    <input type="hidden" name="claims" value="<?php echo CLAIMS; ?>">
    <input type="hidden" name="response_type" value="code" />
    <input type="hidden" name="display" value="page" />
    <input type="hidden" name="prompt" value="consent" />
    <input type="hidden" name="claims_locales" value="<?php echo CLAIMS_LOCALES; ?>" />
    <input type="hidden" name="ui_locales" value="<?php echo UI_LOCALES; ?>" />
    <div>
          <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">eSignet Login</button>
    </div>


    <?php
    include_once './constants.php';
    $pageTitle = "";
    $cookieDetailsFound = true;

if ($cookieDetailsFound) {
    $buttonClass = "relative inline-flex items-center gap-x-1.5 rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600";
    $buttonText = "Logout";
} else {
    $buttonClass = "relative inline-flex items-center gap-x-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600";
    $buttonText = "Login";
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
<div class="bg-white">
  <!-- Header -->
  <header class="absolute inset-x-0 top-0 z-50">
  
  <nav class="bg-black bg-white rounded-lg shadow dark:bg-gray-900 m-4">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 justify-between">
      <div class="flex">
        <div class="-ml-2 mr-2 flex items-center md:hidden">
          <!-- Mobile menu button -->
          <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500" aria-controls="mobile-menu" aria-expanded="false">
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open main menu</span>
            <!--
              Icon when menu is closed.

              Menu open: "hidden", Menu closed: "block"
            -->
            <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <!--
              Icon when menu is open.

              Menu open: "block", Menu closed: "hidden"
            -->
            <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="flex flex-shrink-0 items-center">
          <img class="h-8 w-auto" src="pictures/mosip.png" alt="MOSIP">
        </div>
        <div class="hidden md:ml-6 md:flex md:space-x-8">
          <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
          <!-- <a href="#" class="inline-flex items-center border-b-2 border-indigo-500 px-1 pt-1 text-sm font-medium text-gray-900">Home</a> -->
          <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Home</a>
          <a href="https://docs.esignet.io/" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">eSignet</a>
          <a href="https://mosip.io" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">MOSIP</a>
          <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Profile</a>

        </div>
      </div>
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <button type="button" class="<?php echo $buttonClass; ?>">
            <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            <?php echo $buttonText; ?>
          </button>
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
      <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
      <!-- <a href="#" class="block border-l-4 border-indigo-500 bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700 sm:pl-5 sm:pr-6">Dashboard</a> -->
      <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">Home</a>
      <a href="https://docs.esignet.io/" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">eSignet</a>
      <a href="https://mosip.io" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">MOSIP</a>
      <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700 sm:pl-5 sm:pr-6">Calendar</a>
    </div>
  </div>
</nav>
</header>
</div>