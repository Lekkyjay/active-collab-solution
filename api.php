<?php
require_once __DIR__ . '/vendor/autoload.php';

if (isset($_POST['user']) && isset($_POST['email']) && ($_POST['password'])) {
  $user = $_POST['user'];
  $email = $_POST['email'];
  $password = $_POST['password'];
}

$authenticator = new \ActiveCollab\SDK\Authenticator\Cloud('ACME Inc', 'My Awesome Application', $email, $password);
$accounts = $authenticator->getAccounts();

foreach ($accounts as $account) {
  $account_id = $account['id'];
}


// Issue a token for registered account - this prints out some rubbish
$token = $authenticator->issueToken($account_id);

if ($token instanceof \ActiveCollab\SDK\TokenInterface) {  
  // Create a client instance
  $client = new \ActiveCollab\SDK\Client($token);
} else {
  print "Invalid response\n";
  die();
}

// Make a request
$projects = $client->get('projects')->getJson();
$project_id = $projects[0]['id'];

//get tasks
$tasks = $client->get('projects/'.$project_id.'/tasks')->getJson();
$tasks_arr = $tasks['tasks'];

//Sort tasks in descending order by date
function date_compare($a, $b) {
  $t1 = strtotime($a['created_on']);
  $t2 = strtotime($b['created_on']);
  return $t2 - $t1;
} 
usort($tasks_arr, 'date_compare');

//list sorted tasks
echo '<h1>List of tasks for '.$user.'</h1>';
foreach ($tasks_arr as $task) {
  if ($task['created_by_email'] == $email) {
    echo '<li>'.$task['name'] .'</li>';
  }  
}

?>