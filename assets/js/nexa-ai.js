/**
 * Nexa AI — School Assistant Widget
 * ----------------------------------------------------------------
 * A lightweight, front-end chat assistant for Namugongo Model
 * Primary School. It answers common questions instantly using a
 * knowledge base (no server round-trip needed), and gracefully
 * degrades to a "contact us" suggestion for anything it can't answer.
 *
 * To connect this widget to a real AI backend (e.g. an LLM API),
 * replace the body of `nexaGetAnswer()` with a fetch() call to your
 * own server endpoint, which can then call an AI provider securely
 * server-side (never call a paid AI API with a secret key directly
 * from the browser).
 * ----------------------------------------------------------------
 */
(function () {
    'use strict';

    var KNOWLEDGE_BASE = [
        {
            keywords: ['admission', 'admit', 'apply', 'enroll', 'enrol', 'register', 'join'],
            answer: 'Admissions are open! You can apply anytime through our Admission page — just fill in the parent/guardian and child details and our team will contact you within 2 working days.',
            cta: { label: 'Go to Admission Form', href: 'admission.php' }
        },
        {
            keywords: ['fee', 'fees', 'tuition', 'cost', 'price', 'pay'],
            answer: 'School fees vary by class level and term. For the most accurate, up-to-date fee structure, please contact our office directly — our team will send you the current fees breakdown.',
            cta: { label: 'Contact the School', href: 'contact.php' }
        },
        {
            keywords: ['location', 'address', 'where', 'find', 'directions', 'map'],
            answer: 'We are located in Namugongo, Kampala, Uganda. Reach out on our Contact page and we can share directions or arrange a school visit.',
            cta: { label: 'View Contact Details', href: 'contact.php' }
        },
        {
            keywords: ['contact', 'phone', 'call', 'email', 'reach'],
            answer: 'You can reach the school on +256 702 123 456 or by email at info@namugongomodelschool.edu.ug. We usually respond within one working day.',
            cta: { label: 'Open Contact Page', href: 'contact.php' }
        },
        {
            keywords: ['news', 'event', 'events', 'update', 'happening', 'announcement'],
            answer: 'You can catch up on the latest school news, sports days, and events on our News page — it is updated regularly by the school administration.',
            cta: { label: 'View Latest News', href: 'news.php' }
        },
        {
            keywords: ['gallery', 'photo', 'photos', 'picture', 'pictures'],
            answer: 'Take a look at student life, events, and our campus in the School Gallery.',
            cta: { label: 'Open Gallery', href: 'gallery.php' }
        },
        {
            keywords: ['about', 'mission', 'vision', 'value', 'history'],
            answer: 'Namugongo Model Primary School has shaped young learners for over a decade through strong academics, character formation, and community partnership. Our motto is "Education Is My Future".',
            cta: { label: 'Read More About Us', href: 'about.php' }
        },
        {
            keywords: ['hello', 'hi', 'hey', 'good morning', 'good afternoon'],
            answer: 'Hello! 👋 I am Nexa, your school assistant. I can help with admissions, fees, news, events, or contact details — what would you like to know?'
        },
        {
            keywords: ['thank', 'thanks'],
            answer: 'You are most welcome! Feel free to ask me anything else about the school.'
        }
    ];

    var DEFAULT_ANSWER = "I don't have a specific answer for that yet, but our school office would be glad to help directly.";

    function nexaGetAnswer(query) {
        var q = query.toLowerCase();
        for (var i = 0; i < KNOWLEDGE_BASE.length; i++) {
            var entry = KNOWLEDGE_BASE[i];
            for (var k = 0; k < entry.keywords.length; k++) {
                if (q.indexOf(entry.keywords[k]) !== -1) {
                    return entry;
                }
            }
        }
        return { answer: DEFAULT_ANSWER, cta: { label: 'Contact the School', href: 'contact.php' } };
    }

    function buildWidget() {
        var root = document.getElementById('nexaAiRoot');
        if (!root) return;

        root.innerHTML =
            '<button class="nexa-launcher" id="nexaLauncher" aria-label="Open Nexa AI Assistant">💬</button>' +
            '<div class="nexa-panel" id="nexaPanel" role="dialog" aria-label="Nexa AI School Assistant">' +
            '  <div class="nexa-header">' +
            '    <div class="nexa-header-title"><span class="dot"></span> Nexa AI · School Assistant</div>' +
            '    <button class="nexa-close" id="nexaClose" aria-label="Close">✕</button>' +
            '  </div>' +
            '  <div class="nexa-messages" id="nexaMessages"></div>' +
            '  <div class="nexa-suggestions" id="nexaSuggestions">' +
            '    <button class="nexa-chip" data-q="How do I apply for admission?">Admissions</button>' +
            '    <button class="nexa-chip" data-q="What are the school fees?">Fees</button>' +
            '    <button class="nexa-chip" data-q="How can I contact the school?">Contact</button>' +
            '    <button class="nexa-chip" data-q="What news is there?">News</button>' +
            '  </div>' +
            '  <form class="nexa-input-row" id="nexaForm">' +
            '    <input type="text" id="nexaInput" placeholder="Ask Nexa a question..." autocomplete="off">' +
            '    <button type="submit" class="nexa-send">Send</button>' +
            '  </form>' +
            '</div>';

        var launcher = document.getElementById('nexaLauncher');
        var panel = document.getElementById('nexaPanel');
        var closeBtn = document.getElementById('nexaClose');
        var messages = document.getElementById('nexaMessages');
        var form = document.getElementById('nexaForm');
        var input = document.getElementById('nexaInput');
        var chips = document.querySelectorAll('.nexa-chip');

        function addMessage(text, sender, cta) {
            var el = document.createElement('div');
            el.className = 'nexa-msg ' + sender;
            el.textContent = text;
            messages.appendChild(el);

            if (cta) {
                var link = document.createElement('a');
                link.href = cta.href;
                link.textContent = '→ ' + cta.label;
                link.style.display = 'block';
                link.style.marginTop = '6px';
                link.style.color = '#04223f';
                link.style.fontWeight = '700';
                el.appendChild(link);
            }
            messages.scrollTop = messages.scrollHeight;
        }

        function handleQuery(query) {
            addMessage(query, 'user');
            window.setTimeout(function () {
                var result = nexaGetAnswer(query);
                addMessage(result.answer, 'bot', result.cta);
            }, 350);
        }

        launcher.addEventListener('click', function () {
            panel.classList.toggle('open');
            if (panel.classList.contains('open') && messages.children.length === 0) {
                addMessage('Hi, I am Nexa 👋 — the AI assistant for Namugongo Model Primary School. Ask me about admissions, fees, news, or how to reach us.', 'bot');
            }
        });

        closeBtn.addEventListener('click', function () {
            panel.classList.remove('open');
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var val = input.value.trim();
            if (!val) return;
            handleQuery(val);
            input.value = '';
        });

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                handleQuery(chip.getAttribute('data-q'));
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildWidget);
    } else {
        buildWidget();
    }
})();
