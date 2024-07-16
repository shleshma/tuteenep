<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search filters
$filters = ["verification_status = 'approved'"];
$params = [];

if (!empty($_GET['grade'])) {
    $filters[] = 'EXISTS (SELECT 1 FROM tblsession WHERE tbltutor.tutor_id = tblsession.tutor_id AND FIND_IN_SET(?, session_grade) AND is_booked = 0)';
    $params[] = $_GET['grade'];
}

if (!empty($_GET['subject'])) {
    $filters[] = 'EXISTS (SELECT 1 FROM tblsession WHERE tbltutor.tutor_id = tblsession.tutor_id AND FIND_IN_SET(?, session_subject) AND is_booked = 0)';
    $params[] = $_GET['subject'];
}

$sql = "SELECT * FROM tbltutor";
if (!empty($filters)) {
    $sql .= ' WHERE ' . implode(' AND ', $filters);
}
$sql .= " ORDER BY tutor_name"; // Adding ORDER BY clause

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$tutors = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Tutors</title>
    <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
    <link rel="stylesheet" href="css/findtutors.css">
</head>
<body>
<header>
    <div class="logo">
        <a href="student_dashboard.php"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a>
    </div>
    <div class="menu-container">
        <div class="menu-icon" onclick="toggleMenu()">
            <img src="img/menu.webp" alt="Menu">
        </div>
        <nav>
            <ul>
                <li><a href="findtutors.php">Find Tutors</a></li>
                <li><a href="booking.php">Your Bookings</a></li>
                <li><a href="update_profile.php">Your Profile</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="search-container">
        <h2>Find Tutors</h2>
        <form action="findtutors.php" method="GET">
            <div class="form-group">
                <label for="grade">Grade Level:</label>
                <select id="grade" name="grade">
                    <option value="">Select Grade</option>
                    <option value="9">Grade 9</option>
                    <option value="10">Grade 10</option>
                    <option value="11 Management">Grade 11 (Management)</option>
                    <option value="12 Management">Grade 12 (Management)</option>
                    <option value="11 Science">Grade 11 (Science)</option>
                    <option value="12 Science">Grade 12 (Science)</option>
                    <option value="A1 Non-Science">A1 (Non-Science)</option>
                    <option value="A2 Non-Science">A2 (Non-Science)</option>
                    <option value="A1 Science">A1 (Science)</option>
                    <option value="A2 Science">A2 (Science)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <select id="subject" name="subject">
                    <option value="">Select Subject</option>
                </select>
            </div>
            <div class="form-group submit">
                <button type="submit">Search</button>
            </div>
        </form>
    </div>

    <h2>Tutors</h2>
    <div class="results-container">
        <?php if ($tutors): ?>
            <?php foreach ($tutors as $tutor): ?>
                <div class="tutor">
                    <h2>
                        <?php echo htmlspecialchars($tutor['tutor_name']); ?>
                    </h2>
                    <?php if (!empty($tutor['photo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($tutor['photo_url']); ?>" alt="<?php echo htmlspecialchars($tutor['tutor_name']); ?>'s Photo" class="tutor-photo">
                    <?php else: ?>
                        <img src="img/default_user.webp" alt="<?php echo htmlspecialchars($tutor['tutor_name']); ?>'s Photo" class="tutor-photo">
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($tutor['description']); ?></p>
                    <a  href="#" class="view-details" data-tutor="<?php echo htmlspecialchars(json_encode($tutor)); ?>" style="color: steelblue; font-size: 16px;">View details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-tutors" style="width: 80%; font-weight: lighter; font-size: 15px;">No tutors found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>


<footer class="footer">
    <div class="copyright">
        <p style="color: #338">&copy; TuteeNep 2024</p>
    </div>
    <div class="query">
            <p style="font-size: 14px;">"Empowering Education, Connecting Futures"</p>
    </div>
</footer>

<!-- Modal -->
<div id="tutorModal" class="modal">
    <div class="modal-content">
        <span class="close">×</span>
        <h2 id="modalTutorName"></h2>
        <img id="modalTutorPhoto" src="" alt="Tutor Photo" class="tutor-photo">
        <p><strong>Email:</strong> <span id="modalTutorEmail"></span></p>
        <p><strong>Phone:</strong> <span id="modalTutorPhone"></span></p>
        <p><strong>Description:</strong> <span id="modalTutorDescription"></span></p>
        <p><strong>Subjects:</strong> <span id="modalTutorSubjects"></span></p>
        <p><strong>Address:</strong> <span id="modalTutorLocation"></span></p><br>
        <h3>Upcoming Sessions</h3>
        <div id="modalSessionsContainer" class="sessions-grid"></div>
    </div>
</div>

<script type="text/javascript">
    const subjects = {
        '9': ['Science', 'Social Studies', 'Compulsory Mathematics', 'Environment Population and Health', 'Accountancy', 'Computer Science', 'English', 'Optional Mathematics', 'Nepali'],
        '10': ['Science', 'Social Studies', 'Compulsory Mathematics', 'Environment Population and Health', 'Accountancy', 'Computer Science', 'English', 'Optional Mathematics', 'Nepali'],
        '11 Management': ['English', 'Nepali', 'Social Studies', 'Mathematics', 'Accountancy', 'Economics', 'Computer Science', 'Business Mathematics', 'Hotel Management', 'Business Studies'],
        '12 Management': ['English', 'Nepali', 'Social Studies', 'Mathematics', 'Accountancy', 'Economics', 'Computer Science', 'Business Mathematics', 'Hotel Management', 'Business Studies'],
        '11 Science': ['Nepali', 'English', 'Social Studies', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science'],
        '12 Science': ['Nepali', 'English', 'Social Studies', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science'],
        'A1 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', ''],
        'A1 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)'],
        'A2 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', 'Mathematics'],
        'A2 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)']
    };

    const gradeSelect = document.getElementById('grade');
    const subjectSelect = document.getElementById('subject');

    gradeSelect.addEventListener('change', function() {
        // Clear options in subject select
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';

        // Get selected grade
        const selectedGrade = gradeSelect.value;

        // Populate subjects based on selected grade
        if (selectedGrade in subjects) {
            subjects[selectedGrade].forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        }
    });

    const tutorModal = document.getElementById('tutorModal');
    const closeModal = document.getElementsByClassName('close')[0];

    // Function to toggle modal display
    function toggleModal() {
        tutorModal.style.display = 'block';
    }

    // Close modal when clicking outside modal content
    window.onclick = function(event) {
        if (event.target == tutorModal) {
            tutorModal.style.display = 'none';
        }
    }

    // Close modal when clicking on close button
    closeModal.onclick = function() {
        tutorModal.style.display = 'none';
    }

    // Add event listeners to "View details" links to show modal
    const viewDetailsLinks = document.getElementsByClassName('view-details');

    Array.from(viewDetailsLinks).forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const tutorData = JSON.parse(this.getAttribute('data-tutor'));
            displayTutorModal(tutorData);
        });
    });

    // Function to display tutor modal with data
function displayTutorModal(tutorData) {
    const modalTutorName = document.getElementById('modalTutorName');
    const modalTutorPhoto = document.getElementById('modalTutorPhoto');
    const modalTutorEmail = document.getElementById('modalTutorEmail');
    const modalTutorPhone = document.getElementById('modalTutorPhone');
    const modalTutorDescription = document.getElementById('modalTutorDescription');
    const modalTutorSubjects = document.getElementById('modalTutorSubjects');
    const modalTutorLocation = document.getElementById('modalTutorLocation');
    const modalSessionsContainer = document.getElementById('modalSessionsContainer');

    modalTutorName.textContent = tutorData.tutor_name;
    modalTutorPhoto.src = tutorData.photo_url ? tutorData.photo_url : 'img/default_user.webp';
    modalTutorEmail.textContent = tutorData.tutor_email;
    modalTutorPhone.textContent = tutorData.tutor_phone;
    modalTutorDescription.textContent = tutorData.description;
    modalTutorSubjects.textContent = tutorData.subject;
    modalTutorLocation.textContent = tutorData.address;

    // Inside displayTutorModal function after fetching sessions
fetch('get_sessions.php?tutor_id=' + tutorData.tutor_id)
    .then(response => response.json())
    .then(sessions => {
        modalSessionsContainer.innerHTML = ''; // Clear previous sessions
        if (sessions.length === 0) {
            modalSessionsContainer.innerHTML = '<p style="text-align:left; margin-top: 0px">No available sessions at the moment.</p>';
        } else {
            const sessionsGrid = document.createElement('div');
            sessionsGrid.classList.add('sessions-grid');
            sessions.forEach((session, index) => {
                const sessionDiv = document.createElement('div');
                sessionDiv.classList.add('session');
                let sessionHtml = `
                    <p><strong>Date:</strong> ${session.session_date}</p>
                    <p><strong>Time:</strong> ${new Date('1970-01-01T' + session.session_time + 'Z').toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hour12: true})}</p>
                    <p><strong>Duration:</strong> ${session.session_duration}</p>
                    <p><strong>Grade:</strong> ${session.session_grade}</p>
                    <p><strong>Subject:</strong> ${session.session_subject}</p>
                    <p><strong>Location:</strong> ${session.session_location}</p>
                    <p><strong>Session Type:</strong> ${session.session_type}</p>
                `;
                // Add Additional Note if exists
                if (session.session_note) {
                    sessionHtml += `<p><strong>Note:</strong> ${session.session_note}</p>`;
                }
                
                // Check if seats available for group sessions
                if (session.session_type === 'Group') {
                     sessionHtml += `<p><strong>Group Size:</strong> ${session.group_size}</p>`;
                     sessionHtml += `
                            <form action="book_session.php" method="POST">
                                <input type="hidden" name="session_id" value="${session.session_id}">
                                <button type="submit">Book</button>
                            </form>
                        `;
                    if (session.group_size === null || session.group_size > session.booked_students) {
                        // Display session details
                        sessionHtml += `
                            <form action="book_session.php" method="POST">
                                <input type="hidden" name="session_id" value="${session.session_id}">
                                <button type="submit">Book</button>
                            </form>
                        `;
                    }
                } else {
                    // For One-on-one sessions or sessions without group size limit
                    sessionHtml += `
                        <form action="book_session.php" method="POST">
                            <input type="hidden" name="session_id" value="${session.session_id}">
                            <button type="submit">Book</button>
                        </form>
                    `;
                }
                
                sessionDiv.innerHTML = sessionHtml;
                sessionsGrid.appendChild(sessionDiv);
            });
            modalSessionsContainer.appendChild(sessionsGrid);
        }
    });

    toggleModal();
}

    function toggleMenu() {
        var menuIcon = document.querySelector('.menu-icon');
        menuIcon.classList.toggle('clicked');
    }

</script>

</body>
</html>
