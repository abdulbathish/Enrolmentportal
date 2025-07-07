<?php
include './header.php';
require_once './connection.php';
require_once './helpers/user-session.php';
require_once './helpers/vote.php';
require_once './helpers/jwt-verifier.php';

$loggedInUser = getLoggedInUser($db_conn);

if (JWT_DEBUG_MODE) {
    JwtVerifier::debugLog("Dashboard accessed", [
        'has_logged_in_user' => $loggedInUser !== null,
        'login_type' => $loggedInUser ? $loggedInUser['login_type'] : 'none'
    ]);
}

if ($loggedInUser !== null && $loggedInUser['login_type'] === 'local') {
  $voter = $loggedInUser['user'];
  $voter_ID = $voter['voter_ID'];
  $name = $voter['name'];
  $gender = $voter['gender'];
  $dateOfBirth = $voter['birthdate'];
  $address = "not available";
  $voter_unique_id = $voter_ID;
  $phoneNumber = "not available";
  $email = "not available";
  $dobFmt = 'Y-m-d';
  $status = 'Not Verified';
  $image = 'pictures/nophoto.png';

  if (JWT_DEBUG_MODE) {
      JwtVerifier::debugLog("Local user data loaded", [
          'voter_id' => $voter_ID,
          'name' => $name,
          'gender' => $gender,
          'status' => $status
      ]);
  }
}

if ($loggedInUser !== null && $loggedInUser['login_type'] === 'esignet') {
  $user = $loggedInUser['user'];
  $name = isset($user['name']) ? $user['name'] : "Name Not Present";
  $gender = $user['gender'];
  $dateOfBirth = $user['birthdate'];
  $address = $user['address']['street_address'] . $user['address']['locality'];
  $voter_unique_id = $user['sub'];
  $phoneNumber = isset($user['phone_number']) ? $user['phone_number'] : "Consent Was Not Provided";
  $email = $user['email'];
  $dobFmt = 'Y/m/d';
  $status = 'Verified';
  $image = $user['picture'];

  if (JWT_DEBUG_MODE) {
      JwtVerifier::debugLog("eSignet user data loaded", [
          'sub' => $voter_unique_id,
          'name' => $name,
          'email' => $email,
          'has_phone' => isset($user['phone_number']),
          'status' => $status
      ]);
  }
}

if (isset($_POST['_action']) && $_POST['_action'] = 'insert-vote') {
  if (JWT_DEBUG_MODE) {
      JwtVerifier::debugLog("Vote enrollment attempted", ['voter_unique_id' => $voter_unique_id]);
  }
  insertVoter($db_conn, $voter_unique_id);
}

$alreadyVoted = hasAlreadyVoted($db_conn, $voter_unique_id);
$Elections = array("Lok Sabha 2024", "Kerala State Election");

if (JWT_DEBUG_MODE) {
    JwtVerifier::debugLog("Dashboard data prepared", [
        'already_voted' => $alreadyVoted,
        'is_major' => isPersonMajor($dateOfBirth, $dobFmt),
        'elections_count' => count($Elections)
    ]);
}

// Check JWT verification status (only for eSignet users)
$jwtVerificationStatus = 'NOT_APPLICABLE';
$jwtVerificationError = '';
$jwtVerificationTime = null;

if ($loggedInUser !== null && $loggedInUser['login_type'] === 'esignet') {
    $jwtVerificationStatus = $loggedInUser['user']['_jwt_verification_status'] ?? 'UNKNOWN';
    $jwtVerificationError = $loggedInUser['user']['_jwt_verification_error'] ?? '';
    $jwtVerificationTime = $loggedInUser['user']['_jwt_verification_time'] ?? null;
}

// Function to get status display info
function getVerificationStatusDisplay($status) {
    switch ($status) {
        case 'SUCCESS':
            return ['color' => 'green', 'text' => 'Verified', 'description' => 'JWT signature verified successfully'];
        case 'FAILED':
            return ['color' => 'red', 'text' => 'Verification Failed', 'description' => 'JWT signature verification failed'];
        case 'NOT_APPLICABLE':
            return ['color' => 'blue', 'text' => 'Not Applicable', 'description' => 'Response was plain JSON, not JWT'];
        case 'ERROR':
            return ['color' => 'orange', 'text' => 'Error', 'description' => 'Error occurred during verification'];
        default:
            return ['color' => 'gray', 'text' => 'Unknown', 'description' => 'Verification status unknown'];
    }
}

$statusDisplay = getVerificationStatusDisplay($jwtVerificationStatus);
?>

<style>
.verification-panel {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px auto;
    max-width: 800px;
}

.verification-status {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
}

.status-success { background-color: #d4edda; color: #155724; }
.status-failed { background-color: #f8d7da; color: #721c24; }
.status-not-applicable { background-color: #d1ecf1; color: #0c5460; }
.status-error { background-color: #fff3cd; color: #856404; }
.status-unknown { background-color: #e2e3e5; color: #383d41; }

.verification-details {
    font-size: 14px;
    color: #6c757d;
}

.error-detail {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    padding: 10px;
    margin-top: 10px;
    font-size: 13px;
    color: #721c24;
}

.debug-panel {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin: 20px auto;
    max-width: 800px;
}

.debug-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-right: 10px;
}

.debug-btn:hover {
    background-color: #0056b3;
}
</style>

<div class="mx-auto max-w-7xl pt-0 lg:flex lg:gap-x-16 lg:px-8">
  <aside
    class="flex overflow-x-auto border-b border-gray-900/5 py-4 lg:block lg:w-64 lg:flex-none lg:border-0 lg:py-20">
    <figure class="max-w-lg">
      <?php echo '<img class="h-auto max-w-full rounded-lg" src="' . $image . '" alt="image description">'; ?>
      <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Profile Photo</figcaption>
    </figure>
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
        <p class="mt-1 text-sm leading-6 text-gray-500">
          Information received from
          <?php echo $loggedInUser['login_type'] ?>
          system
        </p>

        <dl class="mt-6 space-y-6 divide-y divide-gray-100 border-t border-gray-200 text-sm leading-6">
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Full name</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $name; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Email address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $email; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Unique ID</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $voter_unique_id; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Date of birth</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $dateOfBirth; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Phone Number</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $phoneNumber; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Gender</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $gender; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <p class="text-gray-900">
                <?php echo $address; ?>
              </p>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500">
                <?php echo $status ?>
              </button>
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
              if (isPersonMajor($dateOfBirth, $dobFmt)) {
                echo '<div class="text-gray-900">Eligible</div>';
              } else {
                echo '<div class="text-gray-900">Not Eligible</div>';
              }

              ?>

              <p class="text-gray-900">
                <?php echo ($alreadyVoted ? "Success" : "Pending") ?>
              </p>
              <form action='#Enroll' method='POST'>
                <input type='hidden' name='_action' value='insert-vote' />
                <button <?php echo ($alreadyVoted ? "disabled" : "") ?> type="submit"
                  class="font-semibold <?php echo ($alreadyVoted ? "text-green-600" : "text-red-600 hover:text-blue-500") ?>  text-md">
                  <?php echo ($alreadyVoted ? "Enrolled" : "Enroll") ?>
                </button>
              </form>

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

<!-- Security Verification Status (only for eSignet users) -->
<?php if ($loggedInUser !== null && $loggedInUser['login_type'] === 'esignet'): ?>
<div class="verification-panel">
    <h3>Security Token Verification Status</h3>
    <div class="verification-status">
        <span class="status-badge status-<?= strtolower(str_replace(['SUCCESS', 'FAILED', 'NOT_APPLICABLE', 'ERROR', 'UNKNOWN'], 
            ['success', 'failed', 'not-applicable', 'error', 'unknown'], $jwtVerificationStatus)) ?>">
            <?= $statusDisplay['text'] ?>
        </span>
        <span><?= $statusDisplay['description'] ?></span>
    </div>
    
    <div class="verification-details">
        <?php if ($jwtVerificationTime): ?>
            <p><strong>Verified at:</strong> <?= date('Y-m-d H:i:s', $jwtVerificationTime) ?></p>
        <?php endif; ?>
        
        <?php if ($jwtVerificationStatus === 'SUCCESS'): ?>
            <p style="color: green;">Your tokens have been cryptographically verified against MOSIP's public keys.</p>
        <?php elseif ($jwtVerificationStatus === 'FAILED'): ?>
            <p style="color: red;">Token verification failed. This could indicate a security issue.</p>
        <?php elseif ($jwtVerificationStatus === 'NOT_APPLICABLE'): ?>
            <p style="color: blue;">The server returned plain JSON instead of JWT tokens.</p>
        <?php elseif ($jwtVerificationStatus === 'UNKNOWN'): ?>
            <p style="color: orange;">Verification status could not be determined.</p>
        <?php endif; ?>
        
        <?php if ($jwtVerificationError): ?>
            <div class="error-detail">
                <strong>Error Details:</strong> <?= htmlspecialchars($jwtVerificationError) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Developer Tools -->
<?php if (JWT_DEBUG_MODE): ?>
<div class="debug-panel">
    <h4>Developer Tools</h4>
    <p>Debug mode is enabled for development and testing purposes.</p>
    <p>
        <a href="test-jwt.php" class="debug-btn">Test Token Verification</a>
        <a href="log-viewer.php" class="debug-btn">View System Logs</a>
        <a href="logout.php" class="debug-btn" style="background-color: #dc3545;">Logout</a>
    </p>
</div>
<?php endif; ?>

<?php
include './footer.php';
?>