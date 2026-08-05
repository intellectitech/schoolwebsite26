<?php
$schoolName    = getSetting($pdo, 'school_name', "Namugongo Parents' School");
$schoolAddress = getSetting($pdo, 'school_address');
$schoolPhone   = getSetting($pdo, 'school_phone');
$schoolEmail   = getSetting($pdo, 'school_email');
$facebookUrl   = getSetting($pdo, 'facebook_url', '#');
$twitterUrl    = getSetting($pdo, 'twitter_url', '#');
$instagramUrl  = getSetting($pdo, 'instagram_url', '#');
?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="about.php" class="nav-link">About Us</a></li>
                    <li><a href="staff.php" class="nav-link">Our Staff</a></li>
                    <li><a href="admissions.php" class="nav-link">Admissions</a></li>
                    <li><a href="fees.php" class="nav-link">School Fees</a></li>
                    <li><a href="news.php" class="nav-link">News & Events</a></li>
                    <li><a href="gallery.php" class="nav-link">Gallery</a></li>
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul>
                    <li><?= htmlspecialchars($schoolAddress) ?></li>
                    <li><?= htmlspecialchars($schoolPhone) ?></li>
                    <li><?= htmlspecialchars($schoolEmail) ?></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="<?= htmlspecialchars($facebookUrl) ?>">Facebook</a></li>
                    <li><a href="<?= htmlspecialchars($twitterUrl) ?>">Twitter</a></li>
                    <li><a href="<?= htmlspecialchars($instagramUrl) ?>">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($schoolName) ?>. All Rights Reserved.
               &nbsp;|&nbsp; <a href="admin/login.php" style="color:inherit">Admin Login</a></p>
        </div>
    </footer>

    <!-- ========== NEXA AI ASSISTANT CHATBOT ========== -->
    <button id="nexaFab" class="chatbot-fab" aria-label="Open Nexa AI Assistant">
        <span style="position:relative;z-index:2;">&#129302;</span>
        <span id="nexaBadge" class="chatbot-badge">1</span>
    </button>

    <div id="nexaWindow" class="chatbot-window" role="dialog" aria-label="Nexa AI Assistant">
        <div class="chatbot-header">
            <div class="chatbot-header-left">
                <div class="chatbot-avatar">&#129302;</div>
                <div class="chatbot-title-wrap">
                    <div class="chatbot-title">Nexa AI Assistant</div>
                    <div class="chatbot-status">Online · Ready to help</div>
                </div>
            </div>
            <button id="nexaClose" class="chatbot-close" aria-label="Close">&times;</button>
        </div>
        <div id="nexaMessages" class="chatbot-messages"></div>
        <div id="nexaQuickReplies" class="chatbot-quick-replies"></div>
        <div class="chatbot-input-wrap">
            <input id="nexaInput" type="text" placeholder="Ask me anything about the school..." autocomplete="off" maxlength="300">
            <button id="nexaSend" class="chatbot-send-btn" aria-label="Send message">&#10148;</button>
        </div>
    </div>

    <script>
    (function() {
        var NEXA = {
            school: {
                name: "Namugongo Parents' Primary School",
                shortName: "NPPS",
                address: "Plot 245, Namugongo Road, Kira Municipality, Wakiso, Uganda",
                phone: "+256 703 072 573",
                email: "info@npps.ac.ug",
                officeHours: "Monday - Friday, 8:00 AM - 5:00 PM",
                motto: "Knowledge, Character, Faith",
                founded: "1998",
                students: "1,200+",
                teachers: "48",
                passRate: "96%",
                classes: ["Baby Class","Middle Class","Top Class","P1","P2","P3","P4","P5","P6","P7"]
            },
            quickReplies: [
                "How do I apply?",
                "School fees?",
                "Contact details",
                "What classes do you offer?",
                "Tell me about the school",
                "Open days?"
            ]
        };

        var msgId = 0;
        var hasWelcomed = false;

        var fab = document.getElementById('nexaFab');
        var win = document.getElementById('nexaWindow');
        var close = document.getElementById('nexaClose');
        var msgBox = document.getElementById('nexaMessages');
        var input = document.getElementById('nexaInput');
        var send = document.getElementById('nexaSend');
        var quickWrap = document.getElementById('nexaQuickReplies');
        var badge = document.getElementById('nexaBadge');

        function scrollBottom() {
            setTimeout(function() {
                msgBox.scrollTop = msgBox.scrollHeight;
            }, 40);
        }

        function nowTime() {
            var d = new Date();
            var h = d.getHours();
            var m = d.getMinutes();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12; if (!h) h = 12;
            m = (m < 10 ? '0' : '') + m;
            return h + ':' + m + ' ' + ampm;
        }

        function addMsg(text, who) {
            msgId++;
            var wrap = document.createElement('div');
            wrap.className = 'chat-msg ' + (who === 'user' ? 'user' : 'bot');
            wrap.id = 'nexa-msg-' + msgId;

            if (who !== 'user') {
                var av = document.createElement('div');
                av.className = 'msg-avatar';
                av.textContent = '\u{1F916}';
                wrap.appendChild(av);
            }

            var bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.textContent = text;
            wrap.appendChild(bubble);

            var col = document.createElement('div');
            col.style.display = 'flex';
            col.style.flexDirection = 'column';
            col.style.alignItems = who === 'user' ? 'flex-end' : 'flex-start';
            col.appendChild(wrap);

            var trow = document.createElement('div');
            trow.className = 'chat-time';
            trow.style.paddingTop = '4px';
            trow.textContent = nowTime();
            if (who === 'user') trow.style.paddingRight = '8px';
            else trow.style.paddingLeft = '38px';
            col.appendChild(trow);

            if (who !== 'user') {
                var fb = document.createElement('div');
                fb.className = 'chatbot-feedback';
                fb.innerHTML = '<span>Helpful?</span>' +
                    '<button class="fb-btn yes" onclick="return NEXA_fb(this,\'yes\')">&#128077;</button>' +
                    '<button class="fb-btn no" onclick="return NEXA_fb(this,\'no\')">&#128078;</button>';
                col.appendChild(fb);
            }

            msgBox.appendChild(col);
            scrollBottom();
            return msgId;
        }

        window.NEXA_fb = function(btn, kind) {
            var wrap = btn.parentElement;
            wrap.querySelectorAll('.fb-btn').forEach(function(b){
                b.classList.add('done');
                b.disabled = true;
            });
            wrap.querySelector('span:first-child').textContent =
                kind === 'yes' ? 'Thanks! \u{1F60A}' : 'Noted, we will improve.';
            return false;
        };

        function addTyping() {
            var row = document.createElement('div');
            row.className = 'chat-msg bot';
            row.id = 'nexa-typing-row';
            var av = document.createElement('div');
            av.className = 'msg-avatar';
            av.textContent = '\u{1F916}';
            var bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.innerHTML = '<div class="chatbot-typing"><span></span><span></span><span></span></div>';
            row.appendChild(av);
            row.appendChild(bubble);
            msgBox.appendChild(row);
            scrollBottom();
        }

        function removeTyping() {
            var r = document.getElementById('nexa-typing-row');
            if (r) r.remove();
        }

        function renderQuickReplies(list) {
            quickWrap.innerHTML = '';
            (list || NEXA.quickReplies).forEach(function(txt) {
                var b = document.createElement('button');
                b.className = 'quick-chip';
                b.type = 'button';
                b.textContent = txt;
                b.addEventListener('click', function() {
                    input.value = txt;
                    sendMsg();
                });
                quickWrap.appendChild(b);
            });
        }

        function greet() {
            addMsg('Hello! I\u2019m Nexa AI, the virtual assistant for ' + NEXA.school.name + '. ' +
                   'I can help with admissions, fees, directions, programs and more. How can I help you today?',
                   'bot');
            renderQuickReplies();
        }

        function openChat(firstOpen) {
            win.classList.add('open');
            badge.style.display = 'none';
            if (!hasWelcomed) {
                hasWelcomed = true;
                setTimeout(greet, 250);
            }
            if (firstOpen) setTimeout(function(){ input.focus(); }, 400);
        }

        function closeChat() {
            win.classList.remove('open');
        }

        fab.addEventListener('click', function() {
            if (!win.classList.contains('open')) openChat(true);
            else closeChat();
        });
        close.addEventListener('click', closeChat);

        function match(text, words) {
            var t = text.toLowerCase();
            return words.some(function(w) { return t.indexOf(w.toLowerCase()) !== -1; });
        }

        function answer(q) {
            var s = NEXA.school;
            var text = (q || '').trim();
            if (!text) return "I didn\u2019t catch that. Could you rephrase, or try one of the quick questions below?";

            if (match(text, ['hi','hello','hey','good morning','good afternoon','greetings','sasa'])) {
                return "Hello! Welcome to " + s.name + ". I\u2019m Nexa AI. Ask me about admissions, fees, " +
                       "programs, directions or anything about the school.";
            }

            if (match(text, ['how are you','how do you do'])) {
                return "I\u2019m running great, thank you! Ready to answer any question about " + s.shortName + ".";
            }

            if (match(text, ['your name','who are you','what are you','nex'])) {
                return "I\u2019m Nexa AI \u{1F916}, the smart assistant for " + s.name +
                       ". I know our programs, admissions process, fees and contact details.";
            }

            if (match(text, ['about','tell me about','school history','founded','vision','mission','motto','story'])) {
                return s.name + " (" + s.shortName + ") was founded in " + s.founded + " by local parents. " +
                       "We are a day primary school (Baby Class to P7) on Namugongo Road near the Martyrs Shrines.\n\n" +
                       "\u{1F3C6} Motto: \u201C" + s.motto + "\u201D\n" +
                       "\u{1F469}\u200D\u{1F393} Pupils: ~" + s.students + "  |  Teachers: " + s.teachers + "\n" +
                       "\u{1F4DA} PLE Div 1&2 pass rate: " + s.passRate;
            }

            if (match(text, ['apply','admission','admissions','enrol','enroll','register','intake','join','application'])) {
                return "To apply to " + s.shortName + ":\n\n" +
                       "1. Submit an enquiry (call, email, or use the Admissions page form)\n" +
                       "2. Collect & complete the application form with required documents\n" +
                       "3. Bring a birth certificate, 2 passport photos, immunisation card & latest report\n" +
                       "4. Attend a short assessment/interview\n" +
                       "5. Receive your admission decision!\n\n" +
                       "Mid-year transfers are accepted. Babies joining Baby Class should be 3+ years by start of Term 1.\n\n" +
                       "\u{1F4DE} Call " + s.phone + " or email " + s.email + " to book an assessment.";
            }

            if (match(text, ['fee','fees','money','cost','price','payment','pay','school fees'])) {
                return "Current termly fees (subject to confirmation with front office):\n\n" +
                       "\u{1F4B5} Nursery (Baby/Middle/Top): UGX 300,000 / term\n" +
                       "\u{1F4B5} Primary 1 \u2013 3:       UGX 400,000 / term\n" +
                       "\u{1F4B5} Primary 4 \u2013 7:       UGX 500,000 / term\n\n" +
                       "Fees include tuition, books and lunch. Transport is optional (ask about routes).\n" +
                       "A sibling discount is available, and instalment plans can be arranged with the bursar.\n\n" +
                       "Please call " + s.phone + " for the latest fees structure.";
            }

            if (match(text, ['class','classes','level','levels','program','programs','curriculum','offer'])) {
                return s.shortName + " offers a full Baby Class to P7 primary programme:\n\n" +
                       "\u{1F476} Nursery: Baby Class, Middle Class, Top Class (play-based early learning)\n" +
                       "\u{1F4DA} Lower Primary: P1 \u2013 P3 (NCDC thematic curriculum, literacy & numeracy focus)\n" +
                       "\u{1F4D6} Upper Primary: P4 \u2013 P7 (standard NCDC curriculum, structured PLE revision)\n\n" +
                       "Average class size: 35\u201340 pupils. Co-curricular: sports, music/dance/drama, debate, clubs, science fair.";
            }

            if (match(text, ['teacher','teachers','staff','head','headteacher','deputy','leadership','team'])) {
                return "Meet our team on the Our Staff page!\n\n" +
                       "We have " + s.teachers + " qualified teachers + support staff.\n" +
                       "Leadership: Head Teacher, Deputy Head / Director of Studies, and our Chaplain.\n\n" +
                       "Small group catch-up sessions are available for learners needing extra support.";
            }

            if (match(text, ['contact','address','phone','email','reach','where is','location','direction','map','visit','open'])) {
                return "Contact " + s.shortName + ":\n\n" +
                       "\u{1F4CD} " + s.address + "\n" +
                       "\u{1F4DE} " + s.phone + "\n" +
                       "\u{2709}\uFE0F " + s.email + "\n" +
                       "\u{1F552} Office: " + s.officeHours + "\n\n" +
                       "Campus is a short distance from the Namugongo Martyrs Shrines (Kira Municipality).\n" +
                       "Visitors welcome any weekday during office hours, or attend our termly Open Day!";
            }

            if (match(text, ['hour','hours','time','open','close','when'])) {
                return s.shortName + " hours:\n\n" +
                       "\u{1F3EB} School day: 7:30 AM \u2013 5:00 PM (Mon\u2013Fri)\n" +
                       "\u{1F4D6} Teaching ends: 4:00 PM, followed by co-curricular clubs\n" +
                       "\u{1F3E2} Front office: " + s.officeHours + "\n\n" +
                       "School van transport runs on selected routes around Namugongo, Kira & Sonde.";
            }

            if (match(text, ['news','event','events','what is happening','latest','upcoming','sports day','concert','parents day'])) {
                return "Please head to the News & Events page on the website for the latest published articles and upcoming school events.\n\n" +
                       "Popular highlights each year:\n" +
                       "\u2022 Annual Inter-House Sports Day\n" +
                       "\u2022 Parents\u2019 Day Celebration\n" +
                       "\u2022 Science & Innovation Fair\n" +
                       "\u2022 Termly Music, Dance & Drama Concert\n" +
                       "\u2022 End of Year Christmas Party";
            }

            if (match(text, ['gallery','photos','photo','pictures','images','show me'])) {
                return "Check out the Photo Gallery page for pictures of school life, sports, classrooms, campus facilities and special events!";
            }

            if (match(text, ['open day','tour','visit','campus tour'])) {
                return "Prospective parents are very welcome to visit!\n\n" +
                       "\u{1F3E2} Drop in any weekday during office hours for a quick tour\n" +
                       "\u{1F4C5} Or attend our termly Open Day (announced on the News page)\n\n" +
                       "Call ahead on " + s.phone + " so we can have a staff member ready to show you around.";
            }

            if (match(text, ['lunch','menu','food','meal','allergy','allergies'])) {
                return "Lunch is included in the fees for all day scholars. If your child has a specific allergy (e.g. peanut allergy), " +
                       "please mention it on the admission form and call the front office so the kitchen can note it.";
            }

            if (match(text, ['boarding','boarding school','hostel','sleep'])) {
                return s.shortName + " is currently a day school only \u2014 we do not offer boarding. Most of our pupils live " +
                       "within Kira Municipality and the wider Namugongo area, and school van transport is available on several routes.";
            }

            if (match(text, ['transport','bus','van','pick up','drop'])) {
                return "Yes! We run school van transport on limited routes around Namugongo, Kira and Sonde. Contact the front office " +
                       "on " + s.phone + " to check whether your area is covered and for the current transport fee.";
            }

            if (match(text, ['thanks','thank you','ok thank','asante'])) {
                return "You\u2019re very welcome! \u{1F642} If there\u2019s anything else, just ask \u2014 or call/email the school directly. " +
                       "Have a wonderful day!";
            }

            if (match(text, ['bye','goodbye','good bye','see you'])) {
                return "Goodbye! Thanks for chatting with Nexa AI. Feel free to come back anytime. " +
                       "Call " + s.phone + " or email " + s.email + " for urgent enquiries \u{1F44B}";
            }

            if (match(text, ['faqs','faq','frequently','question','questions'])) {
                return "Great questions! The website has detailed FAQs on:\n\n" +
                       "1. Admissions (age, transfers, how to apply)\n" +
                       "2. Fees (due dates, what\u2019s included, sibling discount)\n" +
                       "3. Academics (curriculum, class size, extra support)\n" +
                       "4. General (boarding, transport, visiting hours)\n\n" +
                       "Check the Admissions, School Fees and Contact pages for the FAQ lists, or ask me a specific one!";
            }

            return "That\u2019s a great question! I don\u2019t have a specific answer right now, so I\u2019d recommend:\n\n" +
                   "\u{1F4DE} Call the front office: " + s.phone + "\n" +
                   "\u{2709}\uFE0F Email: " + s.email + "\n" +
                   "\u{1F3E2} Or drop by during office hours (" + s.officeHours + ")\n\n" +
                   "Or try asking me in a different way \u2014 I respond well to keywords like " +
                   "\u201Cadmission\u201D, \u201Cfees\u201D, \u201Ccontact\u201D, \u201Cprograms\u201D, \u201Cvisit\u201D.";
        }

        function sendMsg() {
            var text = (input.value || '').trim();
            if (!text) return;
            addMsg(text, 'user');
            input.value = '';
            renderQuickReplies([]);
            addTyping();

            var delay = 650 + Math.min(1800, text.length * 18);
            setTimeout(function() {
                removeTyping();
                var reply = answer(text);
                addMsg(reply, 'bot');
                renderQuickReplies();
                scrollBottom();
            }, delay);
        }

        send.addEventListener('click', sendMsg);
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); sendMsg(); }
        });

        setTimeout(function() {
            badge.style.display = 'flex';
        }, 1500);
    })();
    </script>

    <script src="assets/js/script.js"></script>
</body>
</html>
