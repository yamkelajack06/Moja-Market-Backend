// --- UPDATE PROFILE TESTS ---
echo "\n=== UPDATE PROFILE TESTS ===\n";

// Generate a fresh user to update
$test_id    = uniqid('user_');
$test_email = $test_id . '@test.com';
$test_uname = 'u_' . uniqid();

$registerTest = Register::registerUser(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => $test_uname,
    'email'    => $test_email,
    'password' => 'Test@1234'
]));
echo "REGISTER TEST USER: " . $registerTest->getMessage() . PHP_EOL;

// Should FAIL — username already belongs to someone else
$updateDupUsername = UserProfile::updateProfile(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => 'johhny101',
    'email'    => $test_email,
    'password' => 'Test@1234'
]));
echo "UPDATE WITH TAKEN USERNAME (should fail): " . $updateDupUsername->getMessage() . PHP_EOL;

// Should FAIL — email already belongs to someone else
$updateDupEmail = UserProfile::updateProfile(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => $test_uname,
    'email'    => 'johndoe@gmail.com',
    'password' => 'Test@1234'
]));
echo "UPDATE WITH TAKEN EMAIL (should fail): " . $updateDupEmail->getMessage() . PHP_EOL;

// Should FAIL — both taken
$updateBothTaken = UserProfile::updateProfile(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => 'johhny101',
    'email'    => 'johndoe@gmail.com',
    'password' => 'Test@1234'
]));
echo "UPDATE WITH BOTH TAKEN (should fail): " . $updateBothTaken->getMessage() . PHP_EOL;

// Should PASS — user updating to their own current username and email (no conflict)
$updateSameDetails = UserProfile::updateProfile(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => $test_uname,
    'email'    => $test_email,
    'password' => 'Test@1234'
]));
echo "UPDATE WITH OWN DETAILS (should pass): " . $updateSameDetails->getMessage() . PHP_EOL;

// Should PASS — genuinely new username and email
$newUsername = 'u_updated_' . uniqid();
$newEmail    = 'updated_' . uniqid() . '@test.com';

$updateValid = UserProfile::updateProfile(json_encode([
    'userID'   => $test_id,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => $newUsername,
    'email'    => $newEmail,
    'password' => 'NewPass@456'
]));
echo "UPDATE WITH VALID NEW DETAILS (should pass): " . $updateValid->getMessage() . PHP_EOL;

// Confirm the changes persisted
echo "GET PROFILE AFTER UPDATE: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $test_id]))->toArray()) . PHP_EOL;

echo "\n=== UPDATE PROFILE TESTS COMPLETED ===\n";