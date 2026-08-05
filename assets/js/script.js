
document.addEventListener('DOMContentLoaded', function() {
    console.log('Namugongo Parents School website loaded successfully!');

    // ========== IMAGE FALLBACK FIXER ==========
    // Ensures all broken/invisible images get replaced with proper AI-generated school images
    const schoolImageFallbacks = {
        'images.jpg':   'professional+school+badge+emblem+primary+education+logo',
        'image1.jpg':   'african+primary+school+morning+assembly+students+uniform',
        'image2.jpg':   'happy+school+children+break+time+playing+campus',
        'image3.jpg':   'elementary+school+pupils+walking+campus+grounds',
        'image4.jpg':   'modern+primary+school+building+exterior+architecture',
        'image5.jpg':   'school+sports+day+athletics+children+running+track',
        'image6.jpg':   'school+library+interior+children+reading+books',
        'image7.jpg':   'elementary+classroom+students+teacher+learning',
        'image8.jpg':   'school+examination+hall+students+writing+desk',
        'image9.jpg':   'primary+school+football+team+training+field',
        'image10.jpg':  'school+reception+front+office+administrative',
        'image11.jpg':  'primary+seven+classroom+students+studying+PLE',
        'image12.jpg':  'school+chapel+assembly+hall+interior+design',
        'image13.jpg':  'school+assembly+grounds+outdoor+students+gathered',
        'image14.jpg':  'academic+excellence+graduation+students+celebrating',
        'image15.jpg':  'science+fair+innovation+school+children+projects',
        'image16.jpg':  'parents+day+school+event+ceremony+auditorium',
        'image17.jpg':  'school+open+day+campus+tour+visitors+grounds',
        'image18.jpg':  'school+sports+field+football+pitch+outdoor',
        'image19.jpg':  'cultural+gala+african+traditional+dance+school+children',
        'image20.jpg':  'character+development+students+leadership+training',
        'image21.jpg':  'music+room+school+children+singing+choir+instruments',
        'image22.jpg':  'primary+school+children+playing+playground+recess',
        'image23.jpg':  'christmas+party+school+celebration+children+gifts'
    };

    function getFallbackImage(filename) {
        const baseName = filename.split('/').pop() || 'images.jpg';
        const prompt = schoolImageFallbacks[baseName] || 'primary+school+education+uganda+students';
        return 'https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=' + prompt + '&image_size=landscape_16_9';
    }

    function fixImage(img) {
        if (!img || img.dataset.fixed === '1') return;
        img.dataset.fixed = '1';
        const originalSrc = img.src || '';
        img.onerror = null;
        img.src = getFallbackImage(originalSrc);
    }

    function handleImageError(e) {
        fixImage(e.target);
    }

    document.querySelectorAll('img').forEach(function(img) {
        img.addEventListener('error', handleImageError, { once: true });
        if (img.complete && img.naturalWidth === 0) {
            fixImage(img);
        }
    });

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mut) {
            mut.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    if (node.tagName === 'IMG') {
                        node.addEventListener('error', handleImageError, { once: true });
                        if (node.complete && node.naturalWidth === 0) fixImage(node);
                    } else {
                        node.querySelectorAll && node.querySelectorAll('img').forEach(function(img) {
                            img.addEventListener('error', handleImageError, { once: true });
                            if (img.complete && img.naturalWidth === 0) fixImage(img);
                        });
                    }
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Also scan CSS background images for broken ones
    setTimeout(function() {
        document.querySelectorAll('[style*="background-image"]').forEach(function(el) {
            const bgMatch = el.style.backgroundImage.match(/url\(['"]?(.*?)['"]?\)/);
            if (bgMatch && bgMatch[1]) {
                const testImg = new Image();
                const bgSrc = bgMatch[1];
                testImg.onerror = function() {
                    const fallback = getFallbackImage(bgSrc);
                    el.style.backgroundImage = "url('" + fallback + "')";
                };
                testImg.src = bgSrc;
            }
        });
    }, 500);

    // 0. Header transparency on scroll - show images behind on top, solid when scrolled
    const header = document.querySelector('header');
    function updateHeaderScroll() {
        if (header) {
            if (window.scrollY > 60) {
                header.style.background = 'linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.92) 100%)';
                header.style.borderBottomColor = 'rgba(251, 191, 36, 0.35)';
            } else {
                header.style.background = 'linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 58, 138, 0.7) 100%)';
                header.style.borderBottomColor = 'rgba(251, 191, 36, 0.2)';
            }
        }
    }
    window.addEventListener('scroll', updateHeaderScroll);
    updateHeaderScroll();

    // 1. Add animation when elements come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -60px 0px'
    };

    const scrollObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.feature, .stat, .two-col, .three-col, .message-card, .gallery-grid img').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        scrollObserver.observe(el);
    });

    // 2. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 3. Lightbox for gallery images
    const galleryImages = document.querySelectorAll('.gallery-grid img');
    if (galleryImages.length > 0) {
        const lightbox = document.createElement('div');
        lightbox.id = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <img src="" alt="Full size image" id="lightbox-img">
            </div>
        `;
        document.body.appendChild(lightbox);

        galleryImages.forEach(img => {
            img.addEventListener('click', function() {
                lightbox.style.display = 'flex';
                document.getElementById('lightbox-img').src = this.src;
                document.body.style.overflow = 'hidden';
            });
        });

        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox || e.target.classList.contains('lightbox-close')) {
                lightbox.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// 4. FAQ accordion toggle
document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var item = btn.closest('.faq-item');
        if (item) item.classList.toggle('open');
    });
});

// ========== NEXA AI CHATBOT IMPLEMENTATION ==========
(function() {
    const nexaKnowledgeBase = {
        schoolName: "Namugongo Parents' Primary School (NPPS)",
        motto: "Knowledge, Character, Faith",
        address: "Plot 245, Namugongo Road, Kira Municipality, Wakiso, Uganda",
        phone: "+256 703 072 573",
        email: "info@npps.ac.ug",
        officeHours: "Monday - Friday, 8:00 AM - 5:00 PM",
        founded: "1998",
        heroSubtitle: "Nurturing Every Child Through Excellence In Education, Integrity, And Innovation",
        aboutIntro: "Namugongo Parents' Primary School (NPPS) is a day primary school situated minutes from the Namugongo Martyrs Shrines. Since 1998 we have grown from a small parent-founded nursery into a full Baby Class-to-P7 school known across Kira Municipality for strong PLE results and a warm, disciplined learning community.",
        vision: "To be a leading primary school in Uganda, nurturing confident, disciplined and academically excellent learners equipped for secondary school and for life.",
        mission: "To provide holistic, Christ-centred primary education that develops the intellectual, social, physical and spiritual potential of every child in a safe and caring environment.",
        stats: {
            years: "25+ years of excellence",
            students: "1200+ pupils enrolled",
            passRate: "96% PLE Division 1 & 2 pass rate",
            teachers: "48 qualified teaching staff"
        },
        faqs: [
            { q: ["baby class age", "join baby class", "age for baby", "baby age"], a: "Children joining Baby Class should be at least 3 years old by the start of Term One. We assess school readiness informally during the admissions interview rather than by date of birth alone." },
            { q: ["mid-year transfer", "transfer mid year", "can i transfer", "accept transfers"], a: "Yes. We accept transfers into most classes throughout the year, subject to space and a short placement assessment so we can confirm the right class for your child." },
            { q: ["how to apply", "apply for admission", "application process", "how do i apply"], a: "Visit the school office to collect (or download from this site) an application form, submit it with the required documents, and our admissions office will schedule an assessment and interview date. You can also fill in the enquiry form on the Admissions page." },
            { q: ["when are fees due", "fees due date", "payment due", "deadline fees"], a: "Fees are due at the start of each term, on or before the first day of the term. A payment plan can be arranged with the bursar's office for parents who need to pay in instalments." },
            { q: ["what is included in fees", "fees include", "what do fees cover", "covers fees"], a: "Tuition, instructional materials, and lunch are included for day scholars. Transport is an optional add-on charged separately based on your pick-up zone." },
            { q: ["discount", "sibling discount", "fee discount", "concession"], a: "We offer a sibling discount for families with more than one child enrolled at NPPS at the same time. Speak to the bursar's office for the current rate." },
            { q: ["curriculum", "syllabus", "what curriculum", "teaching method"], a: "We follow the National Curriculum Development Centre (NCDC) thematic curriculum for lower primary and the standard primary curriculum through to Primary Seven, in preparation for PLE." },
            { q: ["class size", "average class", "how many per class", "pupils per class"], a: "We keep class sizes at around 35-40 pupils per stream so teachers can give individual attention, especially during literacy and numeracy lessons in the lower classes." },
            { q: ["extra support", "struggling learners", "remedial", "catch up"], a: "Yes. Class teachers run small-group catch-up sessions during the week, and P5-P7 pupils have access to structured PLE revision support." },
            { q: ["boarding", "boarding school", "hostel", "residential"], a: "No. NPPS is a day school only — we do not currently offer boarding facilities. Most of our pupils live within Kira Municipality and the wider Namugongo area." },
            { q: ["transport", "school bus", "school van", "pick up"], a: "Yes, we run school van transport on a limited number of routes around Namugongo, Kira and Sonde. Ask the front office whether your area is covered." },
            { q: ["lunch rest", "sleep", "quiet time", "rest during lunch"], a: "Yes, each class block has a shaded rest area, and younger pupils in Baby Class and P1 have a supervised quiet-rest period after lunch." },
            { q: ["school hours", "what time", "opening hours", "closing time"], a: "The school day runs from 7:30 AM to 5:00 PM, Monday to Friday, with the main teaching day ending at 4:00 PM followed by co-curricular clubs." },
            { q: ["visit school", "tour", "before enrolling", "can i visit"], a: "Absolutely — we welcome prospective parents to visit any weekday during office hours, or to attend our termly Open Day for a guided tour." },
            { q: ["who to contact", "specific concern", "child concern", "complaint"], a: "Start with your child's class teacher; the front office can direct you to the Deputy Head Teacher or Head Teacher for anything that needs further attention." },
            { q: ["fees structure", "how much fees", "school fees", "cost"], a: "Fees per term: Nursery UGX 300,000; Primary 1-3 UGX 400,000; Primary 4-7 UGX 500,000. Fees are subject to change. Contact us for the latest info. Payment plans available." },
            { q: ["admission requirements", "required documents", "what documents", "bring what"], a: "Required: (1) Birth Certificate original + photocopy, (2) Two recent passport-size photos, (3) Up-to-date immunisation card, (4) Transfer letter if from another school, (5) Most recent report card." },
            { q: ["entry levels", "classes offered", "which classes", "grades"], a: "We offer Baby Class, Middle Class, Top Class (Nursery), and Primary 1 through Primary 7 (P1-P7). PLE preparation begins in P6." },
            { q: ["contact", "phone number", "email", "reach you"], a: "Reach us at Phone: +256 703 072 573, Email: info@npps.ac.ug, Address: Plot 245, Namugongo Road, Kira Municipality, Wakiso, Uganda. Office: Mon-Fri 8AM-5PM." },
            { q: ["head teacher", "headmistress", "principal", "who is head"], a: "The Head Teacher is Mrs. Josephine Nakabuye (M.Ed Educational Management, Makerere University). She has led NPPS since 2015." },
            { q: ["deputy head", "deputy", "dos", "director of studies"], a: "Deputy Head Teacher / Director of Studies is Mr. Peter Ssemwogerere (B.Ed Science, Kyambogo University). He oversees academics, timetable and PLE standards." },
            { q: ["chaplain", "religious", "faith", "pastoral"], a: "Chaplain & Head of RE is Fr. Emmanuel Kirumira (Diploma in Theology, BA Religious Studies, Uganda Martyrs University). All denominations welcome." },
            { q: ["location", "where is", "where are you", "address"], a: "We are at Plot 245, Namugongo Road, Kira Municipality, Wakiso, Uganda — minutes from the Namugongo Martyrs Shrines." },
            { q: ["uniform", "dress code", "what to wear", "school uniform"], a: "NPPS has a school uniform policy. Details and the uniform supplier info are shared with parents on acceptance. Please contact the front office for specifics." },
            { q: ["lunch menu", "food", "meals", "what do you serve"], a: "Nutritious lunch is provided daily. For specific dietary needs or allergies (e.g., peanut allergy), please notify the front office and class teacher so arrangements can be made." },
            { q: ["sports", "games", "football", "co-curricular"], a: "We have athletics, football, netball, music/dance/drama, and various clubs. Wednesday afternoons are dedicated to co-curricular activities." },
            { q: ["ple", "primary leaving", "results", "pass rate"], a: "Our 2025 P7 candidates posted a 96% Division 1 & 2 pass rate — our best ever. P6 and P7 have structured PLE preparation and mock exams." },
            { q: ["hello", "hi", "hey", "good morning", "good afternoon", "good evening"], a: "Hello! 👋 I'm Nexa AI, your NPPS virtual assistant. I can answer questions about admissions, fees, academics, facilities, contact info and more. How can I help you today?" },
            { q: ["thank you", "thanks", "appreciate"], a: "You're most welcome! 😊 Feel free to ask any other questions about Namugongo Parents' Primary School. Have a great day!" },
            { q: ["your name", "who are you", "what are you", "introduce yourself"], a: "I'm **Nexa AI** — your friendly virtual assistant for Namugongo Parents' Primary School. I know all about admissions, fees, academics, events, staff and more. Ask me anything!" },
            { q: ["bye", "goodbye", "see you", "talk later"], a: "Goodbye! 👋 Thank you for visiting NPPS online. Should you have more questions, just click the chat button or email info@npps.ac.ug." },
            { q: ["help", "what can you do", "options", "commands"], a: "I can help with:\n🏫 **About the school** - history, vision, mission, stats\n📝 **Admissions** - process, requirements, entry levels, how to apply\n💰 **Fees** - structure, due dates, payment plans, discounts\n📚 **Academics** - curriculum, PLE results, class sizes, support\n⚽ **Co-curricular** - sports, clubs, music/dance/drama\n📍 **Contact & Location** - address, phone, email, hours\n👨‍🏫 **Staff** - leadership & teaching team\n\nJust type your question!" }
        ],
        quickReplies: [
            "How do I apply?",
            "What are the fees?",
            "Contact info",
            "School hours",
            "PLE results",
            "Entry classes"
        ]
    };

    function findAnswer(question) {
        const q = question.toLowerCase().trim();
        if (!q) return null;
        let bestMatch = null;
        let bestScore = 0;
        for (const entry of nexaKnowledgeBase.faqs) {
            for (const keyword of entry.q) {
                const kw = keyword.toLowerCase();
                let score = 0;
                if (q.includes(kw)) score += kw.length;
                const qWords = q.split(/\s+/);
                const kwWords = kw.split(/\s+/);
                let shared = 0;
                for (const w of kwWords) {
                    if (w.length > 2 && qWords.some(qw => qw === w || qw.startsWith(w) || w.startsWith(qw))) shared++;
                }
                score += shared * 3;
                if (score > bestScore) { bestScore = score; bestMatch = entry; }
            }
        }
        if (bestScore >= 3) return bestMatch.a;
        if (/fee|tuition|cost|price|pay/.test(q) && /how much|amount|structure/.test(q)) return nexaKnowledgeBase.faqs.find(f => f.q[0].includes('fees structure')).a;
        if (/admission|admit|enroll|register/.test(q)) return nexaKnowledgeBase.faqs.find(f => f.q[0].includes('how to apply')).a;
        return null;
    }

    function generateAnswer(question) {
        const direct = findAnswer(question);
        if (direct) return direct;
        const q = question.toLowerCase();
        if (/vision|mission|motto|value|believe/.test(q)) {
            return `🏫 **Our Motto:** ${nexaKnowledgeBase.motto}\n\n👁️ **Our Vision:** ${nexaKnowledgeBase.vision}\n\n🎯 **Our Mission:** ${nexaKnowledgeBase.mission}`;
        }
        if (/about|overview|history|founded|background|tell me about/.test(q)) {
            return `Welcome to **${nexaKnowledgeBase.schoolName}**!\n\n📜 Founded: ${nexaKnowledgeBase.founded} by a group of local parents\n📍 Location: ${nexaKnowledgeBase.address}\n\n${nexaKnowledgeBase.aboutIntro}\n\n🎖️ **By the numbers:**\n• ${nexaKnowledgeBase.stats.years}\n• ${nexaKnowledgeBase.stats.students}\n• ${nexaKnowledgeBase.stats.passRate}\n• ${nexaKnowledgeBase.stats.teachers}`;
        }
        if (/staff|teacher|lecturer|personnel/.test(q)) {
            return `👨‍🏫 **Leadership Team at NPPS:**\n\n• **Mrs. Josephine Nakabuye** — Head Teacher (M.Ed, Makerere; since 2015)\n• **Mr. Peter Ssemwogerere** — Deputy Head / DOS (B.Ed Science, Kyambogo)\n• **Fr. Emmanuel Kirumira** — Chaplain & Head of RE\n• **Ms. Sarah Nansubuga** — P1 Class Teacher (Early Years specialist)\n• **Mrs. Betty Nalubega** — P6 Class Teacher / PLE Prep Coordinator\n• **Mr. David Ochieng** — Games & PE Teacher\n\nTotal: ${nexaKnowledgeBase.stats.teachers} qualified staff. Visit the Our Staff page for full details.`;
        }
        if (/facility|facilities|building|library|lab|computer|classroom|ground/.test(q)) {
            return `🏗️ **Our Campus Facilities:**\n\n• Classroom blocks for Lower & Upper Primary\n• Two-storey Library with 4,000+ books + reading corners\n• Small computer research room\n• Chapel for daily devotion & services\n• Sports field (athletics + football)\n• Dedicated examination hall (PLE mocks + finals)\n• Shaded rest areas for all classes\n\nFor a guided tour, come any weekday 8AM–5PM or join our Open Day!`;
        }
        if (/event|news|upcoming|when|date|calendar|term|parent day|open day/.test(q)) {
            return `📅 **Upcoming Events at NPPS:**\n\n• **Aug 15** — Annual Parents' Day Celebration (Main Hall, 9AM–3PM)\n• **Sep 5** — School Open Day — New Intake (8AM–5PM, all campus)\n• **Sep 22–Oct 3** — End of Term II Examinations (P1–P7)\n• **Oct 10** — Career Guidance & Mentorship Day (P5–P7, Auditorium 10AM)\n• **Oct 28** — Inter-Class Cultural Gala (Sports Ground 9AM)\n• **Dec 5** — End of Year Christmas Party (11AM–4PM)\n\nVisit the News & Events page for full details and news articles!`;
        }
        if (/newsletter|subscribe|stay in touch|update|mailing list/.test(q)) {
            return `📬 Stay in the loop with NPPS!\n\nScroll to the bottom of the Home or News page and use the **Stay In The Loop** newsletter box — just enter your email and click Subscribe. You'll get termly updates on news, events and admissions.`;
        }
        return `Hmm, I don't have a direct answer for that yet. 😊\n\nI can help with:\n• Admissions & Entry Levels\n• Fees & Payments\n• Academics & PLE results\n• Staff, Facilities & Location\n• Contact info & Hours\n• Upcoming Events\n\nTry asking differently, or click a quick reply below! You can also reach us directly at **${nexaKnowledgeBase.phone}** or **${nexaKnowledgeBase.email}**.\n\n— Nexa AI 🤖💙 **${nexaKnowledgeBase.schoolName}**`;
    }

    function formatTime(date) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function initChatbot() {
        if (document.getElementById('nexa-chatbot-fab')) return;

        const fab = document.createElement('button');
        fab.id = 'nexa-chatbot-fab';
        fab.className = 'chatbot-fab';
        fab.innerHTML = '🤖<span class="chatbot-badge">1</span>';
        fab.setAttribute('aria-label', 'Chat with Nexa AI Assistant');
        document.body.appendChild(fab);

        const window = document.createElement('div');
        window.id = 'nexa-chatbot-window';
        window.className = 'chatbot-window';
        window.innerHTML = `
            <div class="chatbot-header">
                <div class="chatbot-header-left">
                    <div class="chatbot-avatar">🤖</div>
                    <div class="chatbot-title-wrap">
                        <div class="chatbot-title">Nexa AI — NPPS Assistant</div>
                        <div class="chatbot-status">Online • School info</div>
                    </div>
                </div>
                <button class="chatbot-close" id="nexa-chatbot-close" aria-label="Close chat">&times;</button>
            </div>
            <div class="chatbot-messages" id="nexa-chatbot-msgs"></div>
            <div class="chatbot-quick-replies" id="nexa-chatbot-quick"></div>
            <div class="chatbot-input-wrap">
                <input type="text" id="nexa-chatbot-input" placeholder="Ask me anything about NPPS..." autocomplete="off">
                <button class="chatbot-send-btn" id="nexa-chatbot-send" aria-label="Send message">➤</button>
            </div>
        `;
        document.body.appendChild(window);

        const msgs = window.querySelector('#nexa-chatbot-msgs');
        const quick = window.querySelector('#nexa-chatbot-quick');
        const input = window.querySelector('#nexa-chatbot-input');
        const sendBtn = window.querySelector('#nexa-chatbot-send');
        const closeBtn = window.querySelector('#nexa-chatbot-close');

        function scrollBottom() { msgs.scrollTop = msgs.scrollHeight; }

        function addMsg(text, sender) {
            const wrap = document.createElement('div');
            wrap.className = 'chat-msg ' + sender;
            const now = new Date();
            if (sender === 'bot') {
                wrap.innerHTML = `
                    <div class="msg-avatar">🤖</div>
                    <div>
                        <div class="chat-bubble"></div>
                        <div class="chat-time">${formatTime(now)}</div>
                        <div class="chatbot-feedback" style="display:none">
                            <span>Helpful?</span>
                            <button class="fb-btn yes" data-fb="yes">👍</button>
                            <button class="fb-btn no" data-fb="no">👎</button>
                        </div>
                    </div>
                `;
                const bubble = wrap.querySelector('.chat-bubble');
                bubble.textContent = text;
                wrap.querySelectorAll('.fb-btn').forEach(b => {
                    b.addEventListener('click', () => {
                        b.classList.add('done');
                        const other = b.classList.contains('yes')
                            ? wrap.querySelector('.fb-btn.no')
                            : wrap.querySelector('.fb-btn.yes');
                        if (other) other.style.display = 'none';
                        b.textContent = b.classList.contains('yes') ? '👍 Thank you!' : '👎 Noted';
                    });
                });
                setTimeout(() => {
                    const fb = wrap.querySelector('.chatbot-feedback');
                    if (fb) fb.style.display = 'flex';
                }, 1200);
            } else {
                wrap.innerHTML = `
                    <div>
                        <div class="chat-bubble"></div>
                        <div class="chat-time">${formatTime(now)}</div>
                    </div>
                `;
                wrap.querySelector('.chat-bubble').textContent = text;
            }
            msgs.appendChild(wrap);
            scrollBottom();
            return wrap;
        }

        function showTyping() {
            const t = document.createElement('div');
            t.className = 'chat-msg bot';
            t.id = 'nexa-typing-indicator';
            t.innerHTML = `
                <div class="msg-avatar">🤖</div>
                <div class="chat-bubble chatbot-typing"><span></span><span></span><span></span></div>
            `;
            msgs.appendChild(t);
            scrollBottom();
        }

        function removeTyping() {
            const t = document.getElementById('nexa-typing-indicator');
            if (t) t.remove();
        }

        function renderQuickReplies(list) {
            quick.innerHTML = '';
            list.forEach(txt => {
                const chip = document.createElement('button');
                chip.className = 'quick-chip';
                chip.textContent = txt;
                chip.addEventListener('click', () => handleSend(txt));
                quick.appendChild(chip);
            });
        }

        function handleSend(text) {
            const message = (text || input.value || '').trim();
            if (!message) return;
            addMsg(message, 'user');
            input.value = '';
            quick.innerHTML = '';
            showTyping();
            setTimeout(() => {
                removeTyping();
                const answer = generateAnswer(message);
                addMsg(answer, 'bot');
                renderQuickReplies(nexaKnowledgeBase.quickReplies);
            }, 550 + Math.random() * 350);
        }

        function toggleOpen(open) {
            const shouldOpen = typeof open === 'boolean' ? open : !window.classList.contains('open');
            window.classList.toggle('open', shouldOpen);
            if (shouldOpen) {
                setTimeout(() => input.focus(), 120);
                const badge = fab.querySelector('.chatbot-badge');
                if (badge) badge.style.display = 'none';
            }
        }

        fab.addEventListener('click', () => toggleOpen());
        closeBtn.addEventListener('click', () => toggleOpen(false));
        sendBtn.addEventListener('click', () => handleSend());
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') handleSend();
        });

        setTimeout(() => {
            addMsg(`👋 Welcome! I'm **Nexa AI**, your virtual assistant for **${nexaKnowledgeBase.schoolName}**.\n\nAsk me anything about:\n• Admissions & Entry Classes\n• School Fees & Payments\n• Academics & PLE results\n• Staff, Facilities & Events\n• Location & Contact info\n\nI'm here to help 24/7! 😊`, 'bot');
            renderQuickReplies(nexaKnowledgeBase.quickReplies);
        }, 800);

        setTimeout(() => {
            const badge = fab.querySelector('.chatbot-badge');
            if (badge && !window.classList.contains('open')) {
                badge.textContent = '1';
            }
        }, 4000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatbot);
    } else {
        initChatbot();
    }
})();
