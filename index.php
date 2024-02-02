<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include './header.php';
$actionUrl = ESIGNET_SERVICE_URL . "/authorize";
function generateNonce($length = 16)
{
  $timestampSalt = time();
  $randomBytes = random_bytes($length - strlen($timestampSalt) / 2);

  return bin2hex($timestampSalt . $randomBytes);
}
$nonce = generateNonce();
?>

<main class="isolate">
  <!-- Hero section -->
  <div class="relative isolate -z-10 overflow-hidden bg-gradient-to-b from-green-400/20 pt-4">
    <div
      class="absolute inset-y-0 right-1/2 -z-10 -mr-96 w-[200%] origin-top-right skew-x-[-30deg] bg-white shadow-xl shadow-green-800/8 ring-1 ring-green-50 sm:-mr-80 lg:-mr-96"
      aria-hidden="true"></div>
    <div class="mx-auto max-w-7xl px-6 py-10 sm:py-15 lg:px-8">
      <div
        class="mx-auto max-w-2xl lg:mx-0 lg:grid lg:max-w-none lg:grid-cols-2 lg:gap-x-16 lg:gap-y-6 xl:grid-cols-1 xl:grid-rows-1 xl:gap-x-8">
        <h1 class="max-w-2xl text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:col-span-2 xl:col-auto">
          Empowering Seamless eSignet Auth Integration for Your PHP-based Web Application</h1>
        <div class="mt-6 max-w-xl lg:mt-0 xl:col-end-1 xl:row-start-1">
          <p class="text-lg leading-8 text-gray-600">This application introduces a user-friendly experience, offering a
            straightforward login process and providing a convenient platform for users to enroll in upcoming elections.
            With a focus on simplicity and efficiency, the system ensures a seamless journey, allowing individuals to
            log in securely and actively participate in the electoral process. Whether you're a new voter or returning
            participant, this application aims to enhance accessibility and engagement in elections, making the
            enrollment process both intuitive and efficient.</p>
        </div>
        <img src="pictures/vote.jpg" alt=""
          class="mt-10 aspect-[6/5] w-full max-w-lg rounded-2xl object-cover sm:mt-16 lg:mt-0 lg:max-w-none xl:row-span-2 xl:row-end-2 xl:mt-10">
      </div>
    </div>
    <div class="absolute inset-x-0 bottom-0 -z-10 h-24 bg-gradient-to-t from-white sm:h-32"></div>
  </div>

  <!-- Timeline section -->
  <div class="mx-auto my-20 max-w-7xl px-6 lg:px-8">
    <div class="mx-auto grid max-w-2xl grid-cols-1 gap-8 overflow-hidden lg:mx-0 lg:max-w-none lg:grid-cols-4">
      <div>
        <time datetime="2021-08" class="flex items-center text-sm font-semibold leading-6 text-green-600">
          <svg viewBox="0 0 4 4" class="mr-4 h-1 w-1 flex-none" aria-hidden="true">
            <circle cx="2" cy="2" r="2" fill="currentColor" />
          </svg>
          Step 1
          <div
            class="absolute -ml-2 h-px w-screen -translate-x-full bg-gray-900/10 sm:-ml-4 lg:static lg:-mr-6 lg:ml-8 lg:w-auto lg:flex-auto lg:translate-x-0"
            aria-hidden="true"></div>
        </time>
        <p class="mt-6 text-lg font-semibold leading-8 tracking-tight text-gray-900">User Authentication</p>
        <p class="mt-1 text-base leading-7 text-gray-600">Enable users to securely log in using eSignet authentication.
        </p>
      </div>
      <div>
        <time datetime="2021-12" class="flex items-center text-sm font-semibold leading-6 text-green-600">
          <svg viewBox="0 0 4 4" class="mr-4 h-1 w-1 flex-none" aria-hidden="true">
            <circle cx="2" cy="2" r="2" fill="currentColor" />
          </svg>
          Step 2
          <div
            class="absolute -ml-2 h-px w-screen -translate-x-full bg-gray-900/10 sm:-ml-4 lg:static lg:-mr-6 lg:ml-8 lg:w-auto lg:flex-auto lg:translate-x-0"
            aria-hidden="true"></div>
        </time>
        <p class="mt-6 text-lg font-semibold leading-8 tracking-tight text-gray-900">Verification of Voter Eligibility
        </p>
        <p class="mt-1 text-base leading-7 text-gray-600">Utilize eSignet VID for flexible and secure eligibility
          verification based on factors like address and age.</p>
      </div>
      <div>
        <time datetime="2022-02" class="flex items-center text-sm font-semibold leading-6 text-green-600">
          <svg viewBox="0 0 4 4" class="mr-4 h-1 w-1 flex-none" aria-hidden="true">
            <circle cx="2" cy="2" r="2" fill="currentColor" />
          </svg>
          Step 3
          <div
            class="absolute -ml-2 h-px w-screen -translate-x-full bg-gray-900/10 sm:-ml-4 lg:static lg:-mr-6 lg:ml-8 lg:w-auto lg:flex-auto lg:translate-x-0"
            aria-hidden="true"></div>
        </time>
        <p class="mt-6 text-lg font-semibold leading-8 tracking-tight text-gray-900">Enrollment for Upcoming Elections
        </p>
        <p class="mt-1 text-base leading-7 text-gray-600">Introduce a seamless enrollment process, allowing voters to
          easily participate in upcoming elections.</p>
      </div>
      <div>
        <time datetime="2022-12" class="flex items-center text-sm font-semibold leading-6 text-green-600">
          <svg viewBox="0 0 4 4" class="mr-4 h-1 w-1 flex-none" aria-hidden="true">
            <circle cx="2" cy="2" r="2" fill="currentColor" />
          </svg>
          Step 4
          <div
            class="absolute -ml-2 h-px w-screen -translate-x-full bg-gray-900/10 sm:-ml-4 lg:static lg:-mr-6 lg:ml-8 lg:w-auto lg:flex-auto lg:translate-x-0"
            aria-hidden="true"></div>
        </time>
        <p class="mt-6 text-lg font-semibold leading-8 tracking-tight text-gray-900">Check Enrollment Status</p>
        <p class="mt-1 text-base leading-7 text-gray-600">Provide voters with the ability to check their enrollment
          status for upcoming elections using eSignet.</p>
      </div>
    </div>
  </div>

  <!-- Logo cloud -->
  <!-- <div class="mx-auto mt-32 max-w-7xl sm:mt-40 sm:px-6 lg:px-8">
      <div class="relative isolate overflow-hidden bg-gray-900 px-6 py-24 text-center shadow-2xl sm:rounded-3xl sm:px-16">
        <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl">eSignet</h2>
        <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">eSignet is a modern digital identity solution designed for secure online access. It enables users to authenticate themselves easily, share profile information, and access online services securely. With support for various identity verification modes, eSignet promotes inclusivity, reducing digital barriers. </p>
        <div class="mx-auto mt-20 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-12 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 sm:gap-y-14 lg:max-w-4xl lg:grid-cols-5">
          <img class="col-span-2 max-h-12 w-full object-contain lg:col-span-1" src="pictures/esignet_logo.png" alt="eSignet" width="158" height="48">
        </div>
        <div class="absolute -top-24 right-0 -z-10 transform-gpu blur-3xl" aria-hidden="true">
          <div class="aspect-[1404/767] w-[87.75rem] bg-gradient-to-r from-[#80caff] to-[#4f46e5] opacity-25" style="clip-path: polygon(73.6% 51.7%, 91.7% 11.8%, 100% 46.4%, 97.4% 82.2%, 92.5% 84.9%, 75.7% 64%, 55.3% 47.5%, 46.5% 49.4%, 45% 62.9%, 50.3% 87.2%, 21.3% 64.1%, 0.1% 100%, 5.4% 51.1%, 21.4% 63.9%, 58.9% 0.2%, 73.6% 51.7%)"></div>
        </div>
      </div>
    </div> -->

</main>
<?php
include './footer.php';
?>