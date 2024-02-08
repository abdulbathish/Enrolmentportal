<?php
include './header.php';
require_once './connection.php';
require_once './helpers/user-session.php';
require_once './helpers/vote.php';

$loggedInUser = getLoggedInUser($db_conn);

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
} 


if ($loggedInUser !== null && $loggedInUser['login_type'] === 'esignet') {
  $user = $loggedInUser['user'];
  $name = isset($user['name']) ? $user['name'] : "name not set" ;
  $gender = $user['gender'];
  $dateOfBirth = $user['birthdate'];
  $address = $user['address']['street_address'] . $user['address']['locality'];
  $voter_unique_id = $user['sub'];
  $phoneNumber = $user['phone_number'];
  $email = $user['email'];
  $dobFmt = 'Y/m/d';
  $status = 'Verified';
}


  if (isset($_POST['_action']) && $_POST['_action'] = 'insert-vote') {
    insertVoter($db_conn, $voter_unique_id);
  }


$alreadyVoted = hasAlreadyVoted($db_conn, $voter_unique_id);
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
        <p class="mt-1 text-sm leading-6 text-gray-500">
          Information received from <?php echo $loggedInUser['login_type'] ?> 
          system
        </p>

        <dl class="mt-6 space-y-6 divide-y divide-gray-100 border-t border-gray-200 text-sm leading-6">
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Full name</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $name; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Email address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $email; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Unique ID</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $voter_unique_id; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Date of birth</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $dateOfBirth; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Phone Number</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $phoneNumber; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Gender</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <div class="text-gray-900">
                <?php echo $gender; ?>
              </div>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
            </dd>
          </div>
          <div class="pt-6 sm:flex">
            <dt class="font-medium text-gray-900 sm:w-64 sm:flex-none sm:pr-6">Address</dt>
            <dd class="mt-1 flex justify-between gap-x-6 sm:mt-0 sm:flex-auto">
              <p class="text-gray-900">
                <?php echo $address; ?>
              </p>
              <button type="button" class="font-semibold text-green-600 hover:text-green-500"><?php echo $status?></button>
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

    <p class="text-gray-900"><?php echo ($alreadyVoted ? "Success" : "Pending") ?></p>
    <form action='#Enroll' method='POST'>
      <input type='hidden' name='_action' value='insert-vote' />
      <button 
        <?php echo ($alreadyVoted ? "disabled" : "") ?> 
        type="submit" 
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
<?php
include './footer.php';
?>

