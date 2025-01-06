<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login/Signup Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-blue-500 to-blue-700 h-screen flex justify-center items-center">

  <!-- Container -->
  <div class="w-96 bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-center mb-6" id="form-heading">Login Form</h2>

    <!-- Tab Buttons -->
    <div class="flex justify-center mb-6">
      <button id="login-tab"
        class="tab-btn px-6 py-2 rounded-l-lg border border-blue-500 bg-blue-500 text-white font-semibold focus:outline-none">
        Login
      </button>
      <button id="signup-tab"
        class="tab-btn px-6 py-2 rounded-r-lg border border-blue-500 bg-white text-blue-500 font-semibold focus:outline-none">
        Signup
      </button>
    </div>

    <!-- Login Form -->
    <form id="login-form" class="space-y-4" action="signinmanagement.php" method="POST">
      <div>
        <input type="email" id="email-login" name="email2" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Email Address" required />
      </div>
      <div>
        <input type="password" id="password-login" name="password2" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Password" required />
      </div>
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold p-3 rounded-lg">
        Login
      </button>
    </form>

    <!-- Signup Form (Initially Hidden) -->
    <form id="signup-form" class="space-y-4 hidden" action="signupmanagement.php" method="POST">
      <div>
        <input type="text" id="firstName" name="firstName" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="First Name" required />
      </div>

      <div>
        <input type="text" id="lastName" name="lastName" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Last Name" required />
      </div>

      <div>
        <input type="email" id="email-signup" name="email2" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Email Address" required />
      </div>

      <div>
        <input type="password" id="password-signup" name="password2" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Password" required />
      </div>

      <div>
        <input type="text" id="phone" name="phone" class="w-full p-3 rounded border border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none" placeholder="Phone Number" />
      </div>

      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold p-3 rounded-lg">
        Signup
      </button>
    </form>
  </div>

  <script>
    const loginTab = document.getElementById("login-tab");
    const signupTab = document.getElementById("signup-tab");
    const formHeading = document.getElementById("form-heading");
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");

    loginTab.addEventListener("click", () => {
      // Show login form and hide signup form
      formHeading.textContent = "Login Form";
      loginForm.classList.remove("hidden");
      signupForm.classList.add("hidden");

      // Style the tabs
      loginTab.classList.add("bg-blue-500", "text-white");
      loginTab.classList.remove("bg-white", "text-blue-500");
      signupTab.classList.remove("bg-blue-500", "text-white");
      signupTab.classList.add("bg-white", "text-blue-500");
    });

    signupTab.addEventListener("click", () => {
      // Show signup form and hide login form
      formHeading.textContent = "Signup Form";
      signupForm.classList.remove("hidden");
      loginForm.classList.add("hidden");

      // Style the tabs
      signupTab.classList.add("bg-blue-500", "text-white");
      signupTab.classList.remove("bg-white", "text-blue-500");
      loginTab.classList.remove("bg-blue-500", "text-white");
      loginTab.classList.add("bg-white", "text-blue-500");
    });
  </script>
</body>

</html>
