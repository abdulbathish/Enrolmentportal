<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['access_token'])) {
  header('Location: login.php');
  exit();
}

include './header.php';
require_once './connection.php';

function decodeUserInfo($encodedUserInfo)
{
  $parts = explode('.', $encodedUserInfo);

  if (isset($parts[1])) {
    $payloadJsonStr = base64_decode($parts[1]);
    $payload = json_decode($payloadJsonStr, true);
    return $payload;
  }
}

function getUserInfo($accessToken)
{
  $url = ESIGNET_SERVICE_URL . "/v1/esignet/oidc/userinfo";
  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
  ]);
  // ENAABLE IN PROD
  // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
  // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);


  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
  if ($httpCode == 200) {
    return decodeUserInfo($response);
  } else {
    echo 'Error: ' . $httpCode;
  }
}

function hasAlreadyVoted($db_conn, $voter_id)
{
  $stmt = $db_conn->prepare("SELECT * FROM voters WHERE voter_id = ?");
  $stmt->bind_param("s", $voter_id);
  $stmt->execute();
  $stmt->store_result();
  return $stmt->num_rows > 0;
}

function isPersonMajor($dateOfBirthString)
{
  $DOB = DateTime::createFromFormat('Y/m/d', $dateOfBirthString);
  $currentDate = new DateTime();

  $age = $currentDate->diff($DOB)->y;

  return $age >= 18;
}

//$userInfo = decodeUserInfo('eyJraWQiOiIxcldMMDJXaE0tZ2JIT2NkRTZzTXprSklaM2ZUSkNMcktxRlBYS3NDd0cwIiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiIyNzMzNTQwMjQ0ODM3MTIzOTg0ODgwMzU0NDY4OTQyMDc0OTEiLCJiaXJ0aGRhdGUiOiIxOTk0LzAxLzAxIiwiYWRkcmVzcyI6eyJzdHJlZXRfYWRkcmVzcyI6ImRmd2VmIHdyZWtuZXJmICAiLCJsb2NhbGl0eSI6IkJlbmdhbHVydSJ9LCJnZW5kZXIiOiJNYWxlIiwiZW1haWwiOiJCQVRISVNIMTIzQEdNQUlMLkNPTSJ9.Zsax1pHacuEHzBG3py9JqgPfKvKmGXD-u684zCs8yjl66mUBdPQjj2wC8ZdQ_IuqQR3eQdf0xbiw4zyxESt2QMsrUBrRacqCi2D8FxZBfBFpP8QOClH26v9WRrVRMHneUwJKuvwjt566hjTGmSGWVcCWCUN6iS03iMg4o6Pr92zs_r-jF_TyyIbsqlnuniSLfZUe0wLbn9b-iw2WVnG2nFmBSpIxa0-gYGlEaob9VroGqyLIO8djXPZji1BY8ZU2wDDn7c0UMrFvSpKErfpmc6TryVYtKvj08sSbaZVqmKS59d5UGGEkMXlhHUjyVU_jXVPG4e_vkQUFGDi24Tc8Nw');



$accessToken = $_COOKIE['access_token'];
$userInfo = getUserInfo($accessToken);
//print_r($userInfo);

$alreadyVoted = hasAlreadyVoted($db_conn, $userInfo['sub']);

if(array_key_exists('name', $userInfo)){
  $name = $userInfo['name'];
}
else {
  $name = 'Name Not Available';
}
$Elections = array("Lok Sabha 2024", "Kerala State Election");
?>


<div class="mx-auto max-w-7xl pt-0 lg:flex lg:gap-x-16 lg:px-8">
  <aside
    class="flex overflow-x-auto border-b border-gray-900/5 py-4 lg:block lg:w-64 lg:flex-none lg:border-0 lg:py-20">
    <nav class="flex-none px-4 sm:px-6 lg:px-0">
      <ul role="list" class="flex gap-x-3 gap-y-1 whitespace-nowrap lg:flex-col">
        <li>
          <a href="#"
            class="bg-gray-300 text-green-600 group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
            <svg class="h-6 w-6 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            User info
          </a>
        </li>
        <li>
          <a href="#Enroll"
            class="text-gray-700 hover:text-green-600 hover:bg-gray-50 group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
            <svg class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-green-600" fill="none" viewBox="0 0 24 24"
              stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33" />
            </svg>
            Enroll
          </a>
        </li>

      </ul>
    </nav>
  </aside>

  <main class="px-4 py-16 sm:px-6 lg:flex-auto lg:px-0 lg:py-20">
    <div class="mx-auto max-w-2xl space-y-16 sm:space-y-20 lg:mx-0 lg:max-w-none">
      <div>
        <!-- <h1 class="text-base font-semibold leading-10 text-gray-900"> -->
        <h2 class="max-w-2xl text-4xl font-semibold text-gray-700 sm:text-2xl lg:col-span-2 xl:col-auto">User Details
        </h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">Information received from eSignet system</p>

        <dl class="mt-6 space-y-6 divide-y divide-gray-100 border-t border-gray-200 text-sm leading-6">
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Full name</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $name; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Email address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['email']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Unique ID</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['sub']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Date of birth</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['birthdate']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Phone Number</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['phone_number']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Gender</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['gender']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $userInfo['address']['street_address']; ?>,
                <?php echo $userInfo['address']['locality']; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">Verified</button>
            </dd>
          </div>
        </dl>
      </div>

      <div>
        <h2 id="Enroll" class="max-w-2xl text-4xl font-semibold text-gray-700 sm:text-2xl lg:col-span-2 xl:col-auto">
          Election Details</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">Upcoming elections</p>
        <dl class="mt-6 space-y-6 divide-y divide-gray-100 border-t border-gray-200 text-sm leading-6">
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">
              <?php echo $Elections[0]; ?>
            </dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">

              <?php
              if (isPersonMajor($userInfo['birthdate'])) {
                echo '<div class="text-gray-900">Eligible</div>';
              } else {
                echo '<div class="text-gray-900">Not Eligible</div>';
              }

              if (!$alreadyVoted) {
                echo '<div class="text-gray-900">Pending</div>';
                echo '<button type="button" class="font-semibold text-red-600 hover:text-blue-500 text-md" onclick="redirectToVote()">Enroll</button>';
              } else {
                echo '<div class="text-gray-900">Success</div>';
                echo '<button type="button" class="font-semibold text-green-600 hover:text-green-500">Enrolled</button>';
              }
              ?>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">
              <?php echo $Elections[1]; ?>
            </dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">Ineligible</div>
              <div class="text-gray-900">Not applicable</div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"></button>
            </dd>
          </div>
        </dl>
      </div>

    </div>
  </main>
</div>
<?php
include './footer.php';
?>
<script>
  function redirectToVote() {
    window.location.href = 'vote.php';
  }
</script>