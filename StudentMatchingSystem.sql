
--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `AID` int(11) NOT NULL,
  `SID` varchar(255) NOT NULL,
  `PID` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `FID` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `facultyName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `PID` int(11) NOT NULL,
  `projectName` varchar(255) NOT NULL,
  `FID` int(11) DEFAULT NULL,
  `ProjDesc` text DEFAULT NULL,
  `SID` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `projstartdate` date NOT NULL,
  `projenddate` date NOT NULL,
  `application_deadline` date NOT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `pid` int(11) NOT NULL,
  `projectName` varchar(255) NOT NULL,
  `projFacultyName` int(11) NOT NULL,
  `projDesc` text NOT NULL,
  `projSkills` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `project_skills`
--

CREATE TABLE `project_skills` (
  `id` int(11) NOT NULL,
  `PID` int(11) NOT NULL,
  `languages` text DEFAULT NULL,
  `tools` text DEFAULT NULL,
  `frameworks` text DEFAULT NULL,
  `proficiency_level` int(11) DEFAULT 1,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `skills_list`
--

CREATE TABLE `skills_list` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `category` enum('languages','tools','frameworks') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `skills_list`
--

INSERT INTO `skills_list` (`skill_id`, `skill_name`, `category`) VALUES
(1, 'Java', 'languages'),
(2, 'Python', 'languages'),
(3, 'C', 'languages'),
(4, 'C++', 'languages'),
(5, 'C#', 'languages'),
(6, 'JavaScript', 'languages'),
(7, 'TypeScript', 'languages'),
(8, 'Swift', 'languages'),
(9, 'Kotlin', 'languages'),
(10, 'Go', 'languages'),
(11, 'Rust', 'languages'),
(12, 'PHP', 'languages'),
(13, 'Ruby', 'languages'),
(14, 'Perl', 'languages'),
(15, 'Dart', 'languages'),
(16, 'R', 'languages'),
(17, 'Scala', 'languages'),
(18, 'Haskell', 'languages'),
(19, 'Lua', 'languages'),
(20, 'Julia', 'languages'),
(21, 'MATLAB', 'languages'),
(22, 'Objective-C', 'languages'),
(23, 'F#', 'languages'),
(24, 'Groovy', 'languages'),
(25, 'Visual Basic', 'languages'),
(26, 'Bash', 'languages'),
(27, 'Shell', 'languages'),
(28, 'PowerShell', 'languages'),
(29, 'Assembly', 'languages'),
(30, 'COBOL', 'languages'),
(31, 'Fortran', 'languages'),
(32, 'Ada', 'languages'),
(33, 'Lisp', 'languages'),
(34, 'Prolog', 'languages'),
(35, 'Scheme', 'languages'),
(36, 'Erlang', 'languages'),
(37, 'Elixir', 'languages'),
(38, 'Racket', 'languages'),
(39, 'Smalltalk', 'languages'),
(40, 'Tcl', 'languages'),
(41, 'VBScript', 'languages'),
(42, 'ABAP', 'languages'),
(43, 'PostScript', 'languages'),
(44, 'AWK', 'languages'),
(45, 'ActionScript', 'languages'),
(46, 'PL/SQL', 'languages'),
(47, 'SQL', 'languages'),
(48, 'Hack', 'languages'),
(49, 'OCaml', 'languages'),
(50, 'Git', 'tools'),
(51, 'Docker', 'tools'),
(52, 'Kubernetes', 'tools'),
(53, 'Jenkins', 'tools'),
(54, 'Travis CI', 'tools'),
(55, 'CircleCI', 'tools'),
(56, 'GitHub Actions', 'tools'),
(57, 'Ansible', 'tools'),
(58, 'Terraform', 'tools'),
(59, 'Puppet', 'tools'),
(60, 'Chef', 'tools'),
(61, 'Vagrant', 'tools'),
(62, 'Nagios', 'tools'),
(63, 'Prometheus', 'tools'),
(64, 'Grafana', 'tools'),
(65, 'Splunk', 'tools'),
(66, 'ELK Stack', 'tools'),
(67, 'New Relic', 'tools'),
(68, 'Postman', 'tools'),
(69, 'Wireshark', 'tools'),
(70, 'Fiddler', 'tools'),
(71, 'Apache JMeter', 'tools'),
(72, 'Selenium', 'tools'),
(73, 'Appium', 'tools'),
(74, 'TestNG', 'tools'),
(75, 'JUnit', 'tools'),
(76, 'Mockito', 'tools'),
(77, 'Cypress', 'tools'),
(78, 'Robot Framework', 'tools'),
(79, 'SonarQube', 'tools'),
(80, 'Nmap', 'tools'),
(81, 'Metasploit', 'tools'),
(82, 'Burp Suite', 'tools'),
(83, 'Aircrack-ng', 'tools'),
(84, 'SQLMap', 'tools'),
(85, 'Ghidra', 'tools'),
(86, 'IDA Pro', 'tools'),
(87, 'Firebase', 'tools'),
(88, 'AWS', 'tools'),
(89, 'Google Cloud', 'tools'),
(90, 'Microsoft Azure', 'tools'),
(91, 'DigitalOcean', 'tools'),
(92, 'Heroku', 'tools'),
(93, 'Netlify', 'tools'),
(94, 'Vercel', 'tools'),
(95, 'NGINX', 'tools'),
(96, 'Apache HTTP Server', 'tools'),
(97, 'Tomcat', 'tools'),
(98, 'IIS', 'tools'),
(99, 'OpenShift', 'tools'),
(100, 'React', 'frameworks'),
(101, 'Angular', 'frameworks'),
(102, 'Vue.js', 'frameworks'),
(103, 'Svelte', 'frameworks'),
(104, 'Next.js', 'frameworks'),
(105, 'Nuxt.js', 'frameworks'),
(106, 'Gatsby', 'frameworks'),
(107, 'Ember.js', 'frameworks'),
(108, 'Backbone.js', 'frameworks'),
(109, 'Meteor', 'frameworks'),
(110, 'Express.js', 'frameworks'),
(111, 'Django', 'frameworks'),
(112, 'Flask', 'frameworks'),
(113, 'FastAPI', 'frameworks'),
(114, 'Spring Boot', 'frameworks'),
(115, 'Hibernate', 'frameworks'),
(116, 'Struts', 'frameworks'),
(117, 'JSF', 'frameworks'),
(118, 'ASP.NET', 'frameworks'),
(119, 'Blazor', 'frameworks'),
(120, 'Ruby on Rails', 'frameworks'),
(121, 'Sinatra', 'frameworks'),
(122, 'Phoenix', 'frameworks'),
(123, 'Laravel', 'frameworks'),
(124, 'Symfony', 'frameworks'),
(125, 'CodeIgniter', 'frameworks'),
(126, 'CakePHP', 'frameworks'),
(127, 'Zend Framework', 'frameworks'),
(128, 'Yii', 'frameworks'),
(129, 'NestJS', 'frameworks'),
(130, 'Koa.js', 'frameworks'),
(131, 'Hapi.js', 'frameworks'),
(132, 'Electron', 'frameworks'),
(133, 'Ionic', 'frameworks'),
(134, 'Cordova', 'frameworks'),
(135, 'Quasar', 'frameworks'),
(136, 'Capacitor', 'frameworks'),
(137, 'TensorFlow', 'frameworks'),
(138, 'PyTorch', 'frameworks'),
(139, 'Scikit-learn', 'frameworks'),
(140, 'Keras', 'frameworks'),
(141, 'Theano', 'frameworks'),
(142, 'MXNet', 'frameworks'),
(143, 'Pandas', 'frameworks'),
(144, 'NumPy', 'frameworks'),
(145, 'OpenCV', 'frameworks'),
(146, 'Unity', 'frameworks'),
(147, 'Unreal Engine', 'frameworks'),
(148, 'Godot', 'frameworks');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `SID` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `year` enum('Undergraduate','Graduate') NOT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `github_link` varchar(255) DEFAULT NULL,
  `website_link` varchar(255) DEFAULT NULL,
  `other_links` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `student_skills`
--

CREATE TABLE `student_skills` (
  `id` int(11) NOT NULL,
  `SID` varchar(255) NOT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`skills`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`AID`),
  ADD KEY `SID` (`SID`),
  ADD KEY `PID` (`PID`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`FID`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`PID`),
  ADD KEY `FID` (`FID`),
  ADD KEY `SID` (`SID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `PID` (`PID`);

--
-- Indexes for table `skills_list`
--
ALTER TABLE `skills_list`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `skill_name` (`skill_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`SID`);

--
-- Indexes for table `student_skills`
--
ALTER TABLE `student_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `SID` (`SID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `AID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `FID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `PID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `project_skills`
--
ALTER TABLE `project_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `skills_list`
--
ALTER TABLE `skills_list`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `student_skills`
--
ALTER TABLE `student_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `students` (`SID`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`PID`) REFERENCES `project` (`PID`) ON DELETE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`FID`) REFERENCES `faculty` (`FID`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`SID`) REFERENCES `students` (`SID`) ON DELETE SET NULL;

--
-- Constraints for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD CONSTRAINT `project_skills_ibfk_1` FOREIGN KEY (`PID`) REFERENCES `project` (`PID`) ON DELETE CASCADE;

--
-- Constraints for table `student_skills`
--
ALTER TABLE `student_skills`
  ADD CONSTRAINT `student_skills_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `students` (`SID`) ON DELETE CASCADE;
COMMIT;
