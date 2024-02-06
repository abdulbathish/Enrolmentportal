<?php
require_once './connection.php';
require 'helpers/local-user.php';
require 'helpers/user-session.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$actionUrl = ESIGNET_SERVICE_URL . "/authorize";
function generateNonce($length = 6)
{
  $timestampSalt = time();
  $randomBytes = random_bytes($length - strlen($timestampSalt) / 2);

  return bin2hex($timestampSalt . $randomBytes);
}
$nonce = generateNonce();


$localLogin = null;
if (isset($_POST["voterid"])) {
  $voter_ID = $_POST["voterid"];
  $password = $_POST["password"];
  $localLogin = verfiyLogin($voter_ID, $password, $db_conn);

}


if ($localLogin !== null && $localLogin['data'] !== null) {
  $_SESSION['local_ID_login'] = $localLogin['data']['voter_ID']; 
}

$loggedInUser = getLoggedInUser($db_conn);

if($loggedInUser !== null) {
  echo "succesfull login";
 header('Location: dashboard.php'); 
 exit();
}


include './header.php';
?>



<body>
  <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <img class="mx-auto h-10 w-auto" src="pictures/logo.png"
        alt="MOSIP">
      <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Voter ID login</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
        <form class="space-y-6" action="#" method="POST">
          <div>
            <label for="voterid" class="block text-sm font-medium leading-6 text-gray-900">Voter ID</label>
            <div class="mt-2">
              <input id="voterid" name="voterid" type="voterid" autocomplete="voterid" required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
            <div class="mt-2">
              <input id="password" name="password" type="password" autocomplete="current-password" required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
              <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">Remember me</label>
            </div>

            <div class="text-sm leading-6">
              <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
            </div>
          </div>

          <div>
            <button type="submit"
              class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
          </div>
        </form>

        <div>
          <div class="relative mt-10">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm font-medium leading-6">
              <span class="bg-white px-6 text-gray-900">Or continue with</span>
            </div>
          </div>

          <div class="my-6 flex relative">
            <form method="get" id="myForm" action="<?php echo $actionUrl; ?>">
              <input type="hidden" name="redirect_uri" value="<?php echo CALLBACK_URL; ?>" />
              <input type="hidden" name="client_id" value="<?php echo CLIENT_ID; ?>" />
              <input type="hidden" name="scope" value="<?php echo SCOPE; ?>" />
              <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
              <input type="hidden" name="state" value="<?php echo STATE; ?>" />
              <input type="hidden" name="acr_values" value="<?php echo ACR_VALUES; ?>" />
              <input type="hidden" name="claims" value='<?php echo CLAIMS; ?>'>
              <input type="hidden" name="response_type" value="code" />
              <input type="hidden" name="display" value="page" />
              <input type="hidden" name="prompt" value="consent" />
              <input type="hidden" name="claims_locales" value="<?php echo CLAIMS_LOCALES; ?>" />
              <input type="hidden" name="ui_locales" value="<?php echo UI_LOCALES; ?>" />
              <div class="flex flex-row">
                <div>
                  <div id="sign-in-with-esignet-standard">
                    <a id="loginButton" href="#">
                      <div class="absolute inset-0 eSignetA eSignetB">
                        <div class="eSignetC">
                          <img src="pictures/esignet_logo.png" class="eSignetD">
                        </div>
                        <span class="eSignetE">Sign in with e-Signet</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

</body>

<?php
include './footer.php';
?>
<script>
  document.getElementById('loginButton').addEventListener('click', function () {
    document.getElementById('myForm').submit();
  });
</script>