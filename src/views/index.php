<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Student Database | Homepage
    </title>

    <script src="./static/js/jquery.js"></script>
    <script src="" </head>

        <body>
            <div id="home-page" class="min-h-screen flex flex-col font-sans bg-background">
                <header class="w-full h-64">
                    <div class="h-full w-full"></div>
                </header>

                <div class="max-w-screen-xl mx-auto my-10 px-5 flex-1">

                    <div class="bg-white rounded-xl p-10 shadow-2xl mb-8">
                        <h2 class="text-3xl text-gray-800 mb-4">
                            Welcome to the Student Database
                        </h2>

                        <p class="text-gray-600 leading-relaxed mb-6">
                            Manage student records efficiently and securely.
                            Access, update, and organize student information
                            all in one place.
                        </p>

                        <div class="flex flex-wrap gap-4">
                            <a href="/students" class="px-6 py-3 rounded-md text-white bg-indigo-500
                           hover:bg-indigo-600 transition transform
                           hover:-translate-y-1 shadow">
                                View Students
                            </a>

                            <a href="/add-student" class="px-6 py-3 rounded-md text-white bg-indigo-500
                           hover:bg-indigo-600 transition transform
                           hover:-translate-y-1 shadow">
                                Add New Student
                            </a>

                            <a href="/settings" class="px-6 py-3 rounded-md border-2 border-indigo-500
                           text-muted bg-gray-100 hover:bg-indigo-500
                           hover:text-white transition transform
                           hover:-translate-y-1">
                                Settings
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">

                        <div class="bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1">
                            <h3 class="text-indigo-500 mb-2">📚 Student Records</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Maintain comprehensive student records with
                                personal information, academic history, and
                                contact details.
                            </p>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1">
                            <h3 class="text-indigo-500 mb-2">📊 Analytics</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Track student performance, enrollment trends,
                                and generate detailed reports for analysis.
                            </p>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-lg text-center transition transform hover:-translate-y-1">
                            <h3 class="text-indigo-500 mb-2">🔒 Security</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Secure access controls and data encryption ensure
                                student information remains protected at all times.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </body>

</html >