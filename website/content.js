/* Al-Qawsan Scientific Bureau - site content, English and Arabic.
   Structure follows the website proposal (proposals/alqawsan-website-proposal.pdf):
   8 tabs, 27 sub-pages, the homepage section order and the linking rules.
   Facts come from the design system brand book (design-system/README.md) and
   public sources. Anything not yet sourced is marked with TBC and rendered as a
   "To be confirmed" badge - never invent a figure, partner or product here. */

(function () {
  "use strict";

  function L(en, ar) { return { en: en, ar: ar }; }
  var TBC = "TBC"; // rendered as a warning badge

  var UI = {
    en: {
      skip: "Skip to content",
      brand: "Al-Qawsan Scientific Bureau",
      brandAlt: "مكتب القوسان العلمي",
      address: "Qadisiyah District, Baghdad, Iraq",
      hours: "Sunday to Thursday, 8:30 to 16:00",
      cta: "Become a Partner",
      search: "Search",
      searchLabel: "Search the site",
      searchPh: "Try “cold chain”, “tenders” or “careers”",
      searchNone: "No page matches “%s”. Try a service name such as “registration”.",
      menu: "Menu",
      close: "Close",
      mainNav: "Main",
      home: "Home",
      breadcrumb: "Breadcrumb",
      inSection: "In this section",
      related: "Related pages",
      readMore: "Read more",
      tbc: "To be confirmed",
      example: "Example",
      copy: "Copy",
      copied: "Copied",
      ctaTitle: "Bringing a product to Iraq?",
      ctaText: "Tell us about your portfolio. Our business development team replies within two working days.",
      ctaContact: "Contact us",
      footerAbout: "Pharmaceutical distribution and scientific promotion across all 18 governorates of Iraq since 2009.",
      group: "Al-Qawsan Group",
      quick: "Quick links",
      services: "Our services",
      contact: "Contact us",
      rights: "All rights reserved.",
      privacy: "Privacy policy",
      terms: "Terms of use",
      sitemap: "Sitemap",
      tagline: "Your Trusted Partner in Medical & Scientific Solutions",
      mapTitle: "Distribution network across Iraq",
      mapHint: "Select a governorate to see its capital and distance from our Baghdad head office.",
      mapHub: "Strategic hub",
      mapHq: "Head office and strategic hub",
      mapServed: "Served governorate",
      mapCapital: "Capital",
      mapDistance: "From Baghdad head office",
      mapKm: "km in a straight line",
      mapHere: "Our head office is in Qadisiyah District, Baghdad.",
      mapGovs: "governorates",
      mapHubs: "strategic hubs",
      prev: "Previous slide",
      next: "Next slide",
      slide: "Slide",
      formRequired: "Fields marked * are required.",
      formPreview: "Preview: this form is not connected yet. In the live site it is sent to %s.",
      formSent: "Thank you. Your request is recorded.",
      formReply: "%s will reply %s.",
      formAgain: "Send another request",
      send: "Send request",
      allAreas: "All areas",
      notFound: "This page does not exist",
      notFoundText: "The link may be out of date. Use the menu or search to find what you need.",
      backHome: "Back to home",
      newsAll: "All news",
      draftSlot: "Article slot",
      draftSlotText: "The next company article will appear here once it is approved for publishing.",
      servicesIntro: "One partner for the whole route to market: registration, pricing, scientific promotion, storage and delivery.",
      partnersIntro: "Manufacturers from Europe, the Middle East, North Africa and Asia trust us with their products in Iraq.",
      areasIntro: "Browse the portfolio by therapeutic area.",
      newsIntro: "Company news and announcements.",
      aboutTeaserH: "An Iraqi scientific bureau built on precision",
      fLocation: "Location",
      fEmail: "Email",
      fCall: "Call now",
      newsTitle: "Our newsletter",
      newsText: "Company news and partner updates, about once a month.",
      newsPh: "name@company.com",
      subscribe: "Subscribe",
      newsPrivacy: "We use your email only to send this newsletter.",
      newsDone: "Thank you. You are subscribed.",
      newsPreview: "Preview: this form is not connected yet.",
      toTop: "Back to top",
      theme: "Theme"
    },
    ar: {
      skip: "انتقل إلى المحتوى",
      brand: "مكتب القوسان العلمي",
      brandAlt: "Al-Qawsan Scientific Bureau",
      address: "حي القادسية، بغداد، العراق",
      hours: "من الأحد إلى الخميس، 8:30 إلى 16:00",
      cta: "كن شريكاً",
      search: "بحث",
      searchLabel: "البحث في الموقع",
      searchPh: "جرّب «سلسلة التبريد» أو «المناقصات» أو «الوظائف»",
      searchNone: "لا توجد صفحة تطابق «%s». جرّب اسم خدمة مثل «التسجيل».",
      menu: "القائمة",
      close: "إغلاق",
      mainNav: "التنقل الرئيسي",
      home: "الرئيسية",
      breadcrumb: "مسار التنقل",
      inSection: "في هذا القسم",
      related: "صفحات ذات صلة",
      readMore: "اقرأ المزيد",
      tbc: "قيد التأكيد",
      example: "مثال",
      copy: "نسخ",
      copied: "تم النسخ",
      ctaTitle: "هل تخطط لإدخال منتجك إلى العراق؟",
      ctaText: "حدّثنا عن محفظة منتجاتك، وسيرد عليك فريق تطوير الأعمال خلال يومَي عمل.",
      ctaContact: "اتصل بنا",
      footerAbout: "توزيع الأدوية والترويج العلمي في جميع محافظات العراق الـ18 منذ عام 2009.",
      group: "مجموعة القوسان",
      quick: "روابط سريعة",
      services: "خدماتنا",
      contact: "اتصل بنا",
      rights: "جميع الحقوق محفوظة.",
      privacy: "سياسة الخصوصية",
      terms: "شروط الاستخدام",
      sitemap: "خريطة الموقع",
      tagline: "شريكك الموثوق في الحلول الطبية والعلمية",
      mapTitle: "شبكة التوزيع في العراق",
      mapHint: "اختر محافظة لعرض مركزها والمسافة من مكتبنا الرئيسي في بغداد.",
      mapHub: "مركز استراتيجي",
      mapHq: "المكتب الرئيسي ومركز استراتيجي",
      mapServed: "محافظة مخدومة",
      mapCapital: "المركز",
      mapDistance: "المسافة من المكتب الرئيسي في بغداد",
      mapKm: "كم بخط مستقيم",
      mapHere: "يقع مكتبنا الرئيسي في حي القادسية ببغداد.",
      mapGovs: "محافظة",
      mapHubs: "مراكز استراتيجية",
      prev: "الشريحة السابقة",
      next: "الشريحة التالية",
      slide: "الشريحة",
      formRequired: "الحقول المعلّمة بـ * مطلوبة.",
      formPreview: "معاينة: هذا النموذج غير مربوط بعد، وفي الموقع الفعلي يُرسل إلى %s.",
      formSent: "شكراً لك، تم تسجيل طلبك.",
      formReply: "سيرد عليك %s %s.",
      formAgain: "إرسال طلب آخر",
      send: "إرسال الطلب",
      allAreas: "جميع المجالات",
      notFound: "هذه الصفحة غير موجودة",
      notFoundText: "قد يكون الرابط قديماً. استخدم القائمة أو البحث للوصول إلى ما تحتاجه.",
      backHome: "العودة إلى الرئيسية",
      newsAll: "جميع الأخبار",
      draftSlot: "مساحة خبر",
      draftSlotText: "سيظهر هنا خبر الشركة التالي بعد اعتماده للنشر.",
      servicesIntro: "شريك واحد للطريق كاملاً إلى السوق: التسجيل والتسعير والترويج العلمي والتخزين والتوزيع.",
      partnersIntro: "شركات مصنّعة من أوروبا والشرق الأوسط وشمال أفريقيا وآسيا تأتمننا على منتجاتها في العراق.",
      areasIntro: "تصفّح المحفظة حسب المجال العلاجي.",
      newsIntro: "أخبار الشركة وإعلاناتها.",
      aboutTeaserH: "مكتب علمي عراقي قائم على الدقة",
      fLocation: "الموقع",
      fEmail: "البريد الإلكتروني",
      fCall: "اتصل الآن",
      newsTitle: "نشرتنا الإخبارية",
      newsText: "أخبار الشركة ومستجدات الشركاء، مرة في الشهر تقريباً.",
      newsPh: "name@company.com",
      subscribe: "اشترك",
      newsPrivacy: "نستخدم بريدك الإلكتروني لإرسال هذه النشرة فقط.",
      newsDone: "شكراً لك، تم اشتراكك.",
      newsPreview: "معاينة: هذا النموذج غير مربوط بعد.",
      toTop: "العودة إلى الأعلى",
      theme: "المظهر"
    }
  };

  /* ------------------------------------------------------------ navigation */
  var NAV = [
    { id: "home", t: L("Home", "الرئيسية") },
    { id: "about", t: L("About Us", "من نحن"),
      kids: ["about-who", "about-vision", "about-history", "about-leadership", "about-quality", "about-group"] },
    { id: "services", t: L("Services", "خدماتنا"),
      kids: ["services-registration", "services-pricing", "services-promotion", "services-cold-chain",
             "services-distribution", "services-tenders"] },
    { id: "partners", t: L("Partners", "شركاؤنا"),
      kids: ["partners-list", "partners-siphat", "partners-join"] },
    { id: "products", t: L("Products", "المنتجات"),
      kids: ["products-areas", "products-list", "products-detail"] },
    { id: "media", t: L("Media Center", "المركز الإعلامي"),
      kids: ["media-news", "media-events", "media-gallery"] },
    { id: "careers", t: L("Careers", "الوظائف"),
      kids: ["careers-why", "careers-positions", "careers-apply"] },
    { id: "contact", t: L("Contact Us", "اتصل بنا"),
      kids: ["contact-office", "contact-branches", "contact-inquiry"] }
  ];

  /* ------------------------------------------------------------ shared data */
  /* Key-figure cards. Every figure is an approved brand fact (design-system README);
     chips state a fact, never an unsourced growth rate. */
  var STATS = [
    { icon: "mapPin", v: "18", label: L("Governorates Served", "المحافظات المخدومة"),
      chip: { icon: "check", t: L("18 of 18", "18 من 18") }, sub: L("Nationwide coverage", "تغطية على مستوى العراق") },
    { icon: "warehouse", v: "5", label: L("Strategic Hubs", "المراكز الاستراتيجية"),
      sub: L("Baghdad, Basra, Erbil, Mosul and Najaf", "بغداد والبصرة وأربيل والموصل والنجف") },
    { icon: "users", v: "1,500+", label: L("Professionals", "الكوادر المهنية"),
      sub: L("Across Al-Qawsan Group", "ضمن مجموعة القوسان") },
    { icon: "handshake", v: "25+", label: L("Manufacturing Partners", "الشركاء المصنّعون"),
      chip: { icon: "arrowUp", t: L("New", "جديد") }, sub: L("SIPHAT, Tunisia (2025)", "SIPHAT، تونس (2025)") }
  ];
  var STATS_BANNER = L(
    "Founded in Baghdad in 2009. Today among Iraq’s top five private pharmaceutical companies, GDP-certified and a partner of the Ministry of Health.",
    "تأسسنا في بغداد عام 2009، ونحن اليوم ضمن أكبر خمس شركات أدوية خاصة في العراق، حاصلون على شهادة ممارسات التوزيع الجيد (GDP) وشركاء لوزارة الصحة."
  );

  var HUBS = ["IQ-BG", "IQ-BA", "IQ-AR", "IQ-NI", "IQ-NA"]; // Baghdad, Basra, Erbil, Mosul (Nineveh), Najaf

  var SERVICES = [
    { id: "services-registration", icon: "fileCheck",
      t: L("Registration & Regulatory Affairs", "التسجيل والشؤون التنظيمية"),
      d: L("We prepare and follow your file with the Ministry of Health until approval.", "نُعدّ ملفك ونتابعه لدى وزارة الصحة حتى الموافقة.") },
    { id: "services-pricing", icon: "chartUp",
      t: L("Pricing & Market Access", "التسعير والنفاذ إلى السوق"),
      d: L("A pricing and launch plan for pharmacies, private hospitals and tenders.", "خطة تسعير وإطلاق للصيدليات والمستشفيات الخاصة والمناقصات.") },
    { id: "services-promotion", icon: "stethoscope",
      t: L("Scientific Promotion", "الترويج العلمي"),
      d: L("Medical representatives who present approved product information to doctors and pharmacists.", "مندوبون علميون يعرضون معلومات المنتج المعتمدة على الأطباء والصيادلة.") },
    { id: "services-cold-chain", icon: "thermometer",
      t: L("Warehousing & Cold Chain", "التخزين وسلسلة التبريد"),
      d: L("GDP-certified storage at room temperature and at 2 to 8 °C.", "تخزين معتمد وفق GDP بدرجة حرارة الغرفة وبين 2 و8 درجات مئوية.") },
    { id: "services-distribution", icon: "truck",
      t: L("Distribution & Logistics", "التوزيع والخدمات اللوجستية"),
      d: L("Delivery to all 18 governorates from five strategic hubs.", "التوصيل إلى المحافظات الـ18 كافة من خمسة مراكز استراتيجية.") },
    { id: "services-tenders", icon: "hospital",
      t: L("Tenders & Public Sector", "المناقصات والقطاع العام"),
      d: L("Supply to public hospitals through Ministry of Health and Kimadia tenders.", "التجهيز للمستشفيات الحكومية عبر مناقصات وزارة الصحة وكيماديا.") }
  ];

  var AREAS = [
    { id: "cardio", icon: "heart", t: L("Cardiology", "أمراض القلب") },
    { id: "diabetes", icon: "droplet", t: L("Diabetes & Endocrinology", "السكري والغدد الصماء") },
    { id: "anti-infectives", icon: "shield", t: L("Anti-infectives", "مضادات العدوى") },
    { id: "respiratory", icon: "wind", t: L("Respiratory", "أمراض الجهاز التنفسي") },
    { id: "gastro", icon: "pill", t: L("Gastroenterology", "أمراض الجهاز الهضمي") },
    { id: "cns", icon: "activity", t: L("Neurology & CNS", "الأعصاب والجهاز العصبي") },
    { id: "oncology", icon: "flask", t: L("Oncology", "الأورام") },
    { id: "derma", icon: "sun", t: L("Dermatology", "الأمراض الجلدية") }
  ];

  var NEWS = [
    { id: "media-siphat", date: "2025-07",
      t: L("Partnership agreement with SIPHAT, Tunisia", "اتفاقية شراكة مع شركة SIPHAT التونسية"),
      d: L("An agreement to export Tunisian-made medicines to Iraq and to transfer pharmaceutical manufacturing know-how.",
           "اتفاقية لتصدير الأدوية التونسية الصنع إلى العراق ونقل خبرات التصنيع الدوائي.") }
  ];

  var FORMS = {
    partner: { team: L("Business Development", "فريق تطوير الأعمال"), when: L("within 2 working days", "خلال يومَي عمل") },
    inquiry: { team: L("Sales and Customer Service", "فريق المبيعات وخدمة العملاء"), when: L("within 1 working day", "خلال يوم عمل واحد") },
    medical: { team: L("Medical Affairs", "فريق الشؤون الطبية"), when: L("within 2 working days", "خلال يومَي عمل") },
    apply: { team: L("Human Resources", "فريق الموارد البشرية"), when: L("after reviewing your application", "بعد مراجعة طلبك") }
  };

  /* ------------------------------------------------------------ home slides */
  var SLIDES = [
    { h: L("Your trusted partner in medical and scientific solutions", "شريكك الموثوق في الحلول الطبية والعلمية"),
      p: L("Since 2009 we have brought certified medicines from international manufacturers to doctors, pharmacies and hospitals in all 18 governorates of Iraq.",
           "منذ عام 2009 نوصل الأدوية المعتمدة من الشركات المصنّعة العالمية إلى الأطباء والصيدليات والمستشفيات في محافظات العراق الـ18 كافة."),
      a: { href: "services-registration", t: L("Explore our services", "تعرّف على خدماتنا") } },
    { h: L("From registration to the pharmacy shelf", "من التسجيل إلى رفّ الصيدلية"),
      p: L("Registration, pricing, scientific promotion, cold-chain storage and delivery, run from five strategic hubs.",
           "التسجيل والتسعير والترويج العلمي والتخزين المبرّد والتوزيع، من خلال خمسة مراكز استراتيجية."),
      a: { href: "partners-list", t: L("Meet our partners", "تعرّف على شركائنا") } },
    { h: L("A new partnership with SIPHAT, Tunisia", "شراكة جديدة مع SIPHAT التونسية"),
      p: L("In July 2025 we signed an agreement to bring Tunisian-made medicines to the Iraqi market.",
           "وقّعنا في تموز 2025 اتفاقية لإدخال الأدوية التونسية الصنع إلى السوق العراقية."),
      a: { href: "media-siphat", t: L("Read the announcement", "اقرأ الخبر") } }
  ];

  /* ------------------------------------------------------------ pages */
  var PAGES = {
    /* ---------------- About */
    "about-who": {
      sec: "about", t: L("Who We Are", "من نحن"),
      lead: L("An Iraqi scientific bureau that brings certified medicines from international manufacturers to patients across the country.",
              "مكتب علمي عراقي يوصل الأدوية المعتمدة من الشركات المصنّعة العالمية إلى المرضى في أنحاء البلاد."),
      blocks: [
        { type: "text", h: L("Our story in brief", "قصتنا باختصار"), p: [
          L("Al-Qawsan Scientific Bureau was founded in Baghdad in 2009. We are now among Iraq’s top five private pharmaceutical companies, with more than 1,500 professionals across the group.",
            "تأسس مكتب القوسان العلمي في بغداد عام 2009، ونحن اليوم ضمن أكبر خمس شركات أدوية خاصة في العراق، ويعمل ضمن مجموعتنا أكثر من <bdi dir=\"ltr\">1,500</bdi> موظف ومختص."),
          L("We work with more than 25 international manufacturers. For each of them we handle registration, pricing, scientific promotion, storage and delivery, and we serve healthcare professionals, pharmacies and the public sector as a partner of the Ministry of Health.",
            "نعمل مع أكثر من 25 شركة مصنّعة دولية، ونتولى لكل منها التسجيل والتسعير والترويج العلمي والتخزين والتوزيع، ونخدم الكوادر الصحية والصيدليات والقطاع العام بصفتنا شريكاً لوزارة الصحة.")
        ] },
        { type: "stats" },
        { type: "features", h: L("Why partners choose Al-Qawsan", "لماذا يختار الشركاء القوسان"), items: [
          { icon: "handshake", t: L("One partner for the whole route", "شريك واحد للطريق كاملاً"), d: L("Registration, pricing, promotion, storage and delivery under one agreement.", "التسجيل والتسعير والترويج والتخزين والتوزيع ضمن اتفاق واحد.") },
          { icon: "stethoscope", t: L("A scientific approach", "نهج علمي"), d: L("Promotion led by pharmacists and medical representatives who work from approved product information.", "ترويج يقوده صيادلة ومندوبون علميون يعتمدون على معلومات المنتج المعتمدة.") },
          { icon: "mapPin", t: L("National reach", "انتشار وطني"), d: L("All 18 governorates, served from five strategic hubs.", "المحافظات الـ18 كافة، من خلال خمسة مراكز استراتيجية.") },
          { icon: "shieldCheck", t: L("Certified quality", "جودة معتمدة"), d: L("GDP-certified storage and handling from receipt to delivery.", "تخزين ونقل معتمدان وفق ممارسات التوزيع الجيد من الاستلام حتى التسليم.") }
        ] }
      ]
    },
    "about-vision": {
      sec: "about", t: L("Vision, Mission & Values", "الرؤية والرسالة والقيم"),
      lead: L("What we are working towards, and the values that shape how we work.", "ما نسعى إليه، والقيم التي تحدد طريقة عملنا."),
      blocks: [
        { type: "pair", items: [
          { t: L("Our vision", "رؤيتنا"), d: L("To be Iraq’s most trusted partner for bringing safe, effective medicines to every patient who needs them.", "أن نكون الشريك الأكثر ثقة في العراق لإيصال الأدوية الآمنة والفعّالة إلى كل مريض يحتاجها.") },
          { t: L("Our mission", "رسالتنا"), d: L("To connect international manufacturers with Iraqi healthcare through reliable registration, ethical scientific promotion and careful distribution.", "أن نربط الشركات المصنّعة العالمية بالقطاع الصحي العراقي من خلال تسجيل موثوق وترويج علمي أخلاقي وتوزيع دقيق.") }
        ] },
        { type: "values", h: L("Our values", "قيمنا"), items: [
          { t: L("Commitment", "الالتزام"), d: L("We deliver what we promise, on time.", "نفي بما نعد به في موعده.") },
          { t: L("Quality of Work", "الجودة في العمل"), d: L("Every batch is stored, handled and documented to GDP.", "نخزّن كل تشغيلة وننقلها ونوثقها وفق ممارسات التوزيع الجيد.") },
          { t: L("Teamwork", "العمل الجماعي"), d: L("Regulatory, medical, sales and logistics teams work as one.", "تعمل فرق الشؤون التنظيمية والطبية والمبيعات واللوجستيات كفريق واحد.") },
          { t: L("Continuous Development", "التطوير المستمر"), d: L("Our people train continuously in science and compliance.", "يتدرب فريقنا باستمرار في العلوم والامتثال.") },
          { t: L("Responsibility", "المسؤولية"), d: L("We promote only what approved product information supports.", "لا نروّج إلا لما تدعمه معلومات المنتج المعتمدة.") },
          { t: L("Discipline", "الانضباط"), d: L("Clear procedures, followed every day.", "إجراءات واضحة نلتزم بها كل يوم.") }
        ] }
      ]
    },
    "about-history": {
      sec: "about", t: L("Our History", "مسيرتنا"),
      lead: L("From one office in Baghdad in 2009 to a national network of five strategic hubs.", "من مكتب واحد في بغداد عام 2009 إلى شبكة وطنية من خمسة مراكز استراتيجية."),
      blocks: [
        { type: "timeline", items: [
          { y: "2009", t: L("Founded in Baghdad", "التأسيس في بغداد"), d: L("Al-Qawsan Scientific Bureau opens in Baghdad.", "افتتاح مكتب القوسان العلمي في بغداد.") },
          { y: "2015", t: L("Lara Scientific Office", "مكتب لارا العلمي"), d: L("Lara Scientific Office becomes part of the group.", "انضمام مكتب لارا العلمي إلى المجموعة.") },
          { y: "2019", t: L("Sanaya Scientific Office", "مكتب سنايا العلمي"), d: L("Sanaya Scientific Office becomes part of the group.", "انضمام مكتب سنايا العلمي إلى المجموعة.") },
          { y: "2025", t: L("Partnership with SIPHAT", "الشراكة مع SIPHAT"), d: L("Agreement to bring Tunisian-made medicines to Iraq.", "اتفاقية لإدخال الأدوية التونسية الصنع إلى العراق.") },
          { y: L("Today", "اليوم"), t: L("Five strategic hubs", "خمسة مراكز استراتيجية"), d: L("Baghdad, Basra, Erbil, Mosul and Najaf serve all 18 governorates.", "بغداد والبصرة وأربيل والموصل والنجف تخدم المحافظات الـ18 كافة.") }
        ] }
      ]
    },
    "about-leadership": {
      sec: "about", t: L("Leadership Team", "فريق القيادة"),
      lead: L("The people responsible for our partners, our quality and our teams.", "المسؤولون عن شركائنا وجودة عملنا وفرقنا."),
      blocks: [
        { type: "people", items: [
          L("General Manager", "المدير العام"),
          L("Scientific Director", "المدير العلمي"),
          L("Regulatory Affairs Manager", "مدير الشؤون التنظيمية"),
          L("Supply Chain Manager", "مدير سلسلة الإمداد")
        ] },
        { type: "note", text: L("Names, portraits and short biographies will be added once approved by management.", "ستُضاف الأسماء والصور والسير المختصرة بعد اعتمادها من الإدارة.") }
      ]
    },
    "about-quality": {
      sec: "about", t: L("Quality & Compliance", "الجودة والامتثال"),
      lead: L("GDP-certified storage and handling, with every batch traceable from receipt to delivery.", "تخزين ونقل معتمدان وفق ممارسات التوزيع الجيد، مع إمكانية تتبّع كل تشغيلة من الاستلام حتى التسليم."),
      blocks: [
        { type: "features", items: [
          { icon: "shieldCheck", t: L("Good Distribution Practice (GDP)", "ممارسات التوزيع الجيد (GDP)"), d: L("Controlled storage, traceable batches and documented handling. Al-Qawsan is GDP-certified.", "تخزين خاضع للرقابة وتشغيلات قابلة للتتبع ونقل موثّق، ومكتب القوسان حاصل على شهادة GDP.") },
          { icon: "thermometer", t: L("Temperature control", "ضبط درجات الحرارة"), d: L("Room-temperature storage at 15 to 25 °C and refrigerated storage at 2 to 8 °C.", "تخزين بدرجة حرارة الغرفة بين 15 و25 درجة مئوية، وتخزين مبرّد بين 2 و8 درجات مئوية.") },
          { icon: "activity", t: L("Pharmacovigilance", "اليقظة الدوائية"), d: L("Adverse-event reports are collected and passed to the manufacturer and the Iraqi Pharmacovigilance Center.", "نجمع تقارير الأعراض الجانبية ونحيلها إلى الشركة المصنّعة والمركز العراقي لليقظة الدوائية.") },
          { icon: "fileCheck", t: L("Recall procedure", "إجراء السحب"), d: L("A documented procedure to trace and withdraw any batch quickly.", "إجراء موثّق لتتبّع أي تشغيلة وسحبها بسرعة.") }
        ] },
        { type: "note", text: L("Certificates and audit reports will be published here as PDF files.", "ستُنشر الشهادات وتقارير التدقيق هنا بصيغة PDF.") }
      ]
    },
    "about-group": {
      sec: "about", t: L("Group Companies", "شركات المجموعة"),
      lead: L("Al-Qawsan Scientific Bureau is part of Al-Qawsan Group.", "مكتب القوسان العلمي جزء من مجموعة القوسان."),
      blocks: [ { type: "group" } ]
    },

    /* ---------------- Services */
    "services-registration": {
      sec: "services", t: SERVICES[0].t, icon: "fileCheck",
      lead: L("We prepare your registration file and follow it with the Iraqi Ministry of Health until the product is approved for sale.", "نُعدّ ملف تسجيل منتجك ونتابعه لدى وزارة الصحة العراقية حتى الموافقة على تداوله."),
      blocks: [
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("Dossier review and gap analysis against Iraqi requirements", "مراجعة الملف وتحليل النواقص وفق المتطلبات العراقية"),
          L("Company and product registration submissions", "تقديم طلبات تسجيل الشركة والمنتج"),
          L("Samples, labelling and Arabic leaflet coordination", "تنسيق العينات والملصقات والنشرة العربية"),
          L("Variations, renewals and regulatory correspondence", "التعديلات والتجديدات والمراسلات التنظيمية")
        ] },
        { type: "steps", h: L("How registration works", "كيف يجري التسجيل"), items: [
          { t: L("Review", "المراجعة"), d: L("We check your dossier against current requirements.", "نراجع ملفك وفق المتطلبات الحالية.") },
          { t: L("Company file", "ملف الشركة"), d: L("We submit the manufacturer registration.", "نقدّم طلب تسجيل الشركة المصنّعة.") },
          { t: L("Product file", "ملف المنتج"), d: L("We submit each product with samples and documents.", "نقدّم كل منتج مع العينات والوثائق.") },
          { t: L("Approval", "الموافقة"), d: L("We follow the evaluation until approval and pricing.", "نتابع التقييم حتى الموافقة والتسعير.") }
        ] }
      ],
      rel: ["services-pricing", "partners-list", "partners-join"]
    },
    "services-pricing": {
      sec: "services", t: SERVICES[1].t, icon: "chartUp",
      lead: L("We build a pricing and access plan for the private market and for public tenders.", "نضع خطة تسعير ونفاذ إلى السوق الخاص والمناقصات الحكومية."),
      blocks: [
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("Price proposal preparation for the Ministry of Health", "إعداد مقترح السعر لوزارة الصحة"),
          L("Market and competitor analysis", "تحليل السوق والمنافسين"),
          L("Channel plan: pharmacies, private hospitals and the public sector", "خطة القنوات: الصيدليات والمستشفيات الخاصة والقطاع العام"),
          L("Launch planning with the promotion team", "تخطيط الإطلاق مع فريق الترويج")
        ] }
      ],
      rel: ["services-registration", "services-tenders", "partners-join"]
    },
    "services-promotion": {
      sec: "services", t: SERVICES[2].t, icon: "stethoscope",
      lead: L("Our medical representatives present your products to doctors and pharmacists with accurate, approved information.", "يعرض مندوبونا العلميون منتجاتك على الأطباء والصيادلة بمعلومات دقيقة ومعتمدة."),
      blocks: [
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("Field visits by trained medical representatives", "زيارات ميدانية يقوم بها مندوبون علميون مدرّبون"),
          L("Continuing medical education (CME) meetings", "لقاءات التعليم الطبي المستمر"),
          L("Presence at scientific events and conferences", "المشاركة في الفعاليات والمؤتمرات العلمية"),
          L("Regular field reports for each partner", "تقارير ميدانية دورية لكل شريك")
        ] },
        { type: "note", text: L("We never promise a medical outcome. Every message is based on approved product information and Ministry of Health registration.", "لا نعد بأي نتيجة علاجية، وكل رسالة نقدّمها تستند إلى معلومات المنتج المعتمدة وتسجيله لدى وزارة الصحة.") }
      ],
      rel: ["products-areas", "media-events", "partners-join"]
    },
    "services-cold-chain": {
      sec: "services", t: SERVICES[3].t, icon: "thermometer",
      lead: L("Products are stored and handled under controlled conditions, from arrival in Iraq to dispatch.", "نخزّن المنتجات وننقلها في ظروف خاضعة للرقابة من وصولها إلى العراق حتى شحنها."),
      blocks: [
        { type: "temps" },
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("GDP-certified warehousing", "تخزين معتمد وفق ممارسات التوزيع الجيد"),
          L("Continuous temperature logging for cold-chain products", "تسجيل مستمر لدرجات الحرارة لمنتجات سلسلة التبريد"),
          L("Batch and expiry tracking: first expiry, first out", "تتبّع التشغيلات وتواريخ الانتهاء: الأقرب انتهاءً يُصرف أولاً"),
          L("Receiving inspection and customs coordination", "فحص الاستلام والتنسيق الجمركي")
        ] }
      ],
      rel: ["services-distribution", "about-quality", "partners-join"]
    },
    "services-distribution": {
      sec: "services", t: SERVICES[4].t, icon: "truck",
      lead: L("We deliver to pharmacies, private hospitals and public institutions in all 18 governorates.", "نوصل المنتجات إلى الصيدليات والمستشفيات الخاصة والمؤسسات الحكومية في المحافظات الـ18 كافة."),
      blocks: [
        { type: "map" },
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("Scheduled routes from five strategic hubs", "مسارات منتظمة من خمسة مراكز استراتيجية"),
          L("Temperature-controlled transport for cold-chain products", "نقل مبرّد لمنتجات سلسلة التبريد"),
          L("Proof of delivery and returns handling", "إثبات التسليم ومعالجة المرتجعات")
        ] }
      ],
      rel: ["services-cold-chain", "contact-branches", "partners-join"]
    },
    "services-tenders": {
      sec: "services", t: SERVICES[5].t, icon: "hospital",
      lead: L("We support public-sector supply through tenders from the Ministry of Health and Kimadia.", "ندعم تجهيز القطاع العام عبر مناقصات وزارة الصحة وكيماديا."),
      blocks: [
        { type: "list", h: L("What we do", "ما نقوم به"), items: [
          L("Tender monitoring and document preparation", "متابعة المناقصات وإعداد الوثائق"),
          L("Aligning registration and pricing with tender specifications", "مواءمة التسجيل والتسعير مع مواصفات المناقصة"),
          L("Delivery to public hospitals and health directorates", "التوصيل إلى المستشفيات الحكومية ودوائر الصحة"),
          L("Follow-up after award", "المتابعة بعد الإحالة")
        ] }
      ],
      rel: ["services-pricing", "contact-inquiry", "partners-join"]
    },

    /* ---------------- Partners */
    "partners-list": {
      sec: "partners", t: L("International Partners", "شركاؤنا الدوليون"),
      lead: L("More than 25 manufacturers trust us with their products in Iraq.", "أكثر من 25 شركة مصنّعة تأتمننا على منتجاتها في العراق."),
      blocks: [ { type: "partners" } ],
      rel: ["partners-siphat", "products-list", "partners-join"]
    },
    "partners-siphat": {
      sec: "partners", t: L("Partner profile: SIPHAT", "ملف الشريك: SIPHAT"),
      lead: L("Société des Industries Pharmaceutiques de Tunisie, a Tunisian pharmaceutical manufacturer.", "الشركة التونسية للصناعات الصيدلانية، وهي شركة تونسية لتصنيع الأدوية."),
      blocks: [
        { type: "text", h: L("Our partnership", "شراكتنا"), p: [
          L("In July 2025 SIPHAT and Al-Qawsan signed an agreement, in the presence of Tunisia’s Minister of Health, to export Tunisian-made medicines to the Iraqi market and to transfer pharmaceutical manufacturing technology.",
            "في تموز 2025 وقّعت SIPHAT ومكتب القوسان العلمي، بحضور وزير الصحة التونسي، اتفاقية لتصدير الأدوية التونسية الصنع إلى السوق العراقية ونقل تكنولوجيا التصنيع الدوائي.")
        ] },
        { type: "facts", items: [
          { k: L("Country", "البلد"), v: L("Tunisia", "تونس") },
          { k: L("Partner since", "شريك منذ"), v: L("2025", "2025") },
          { k: L("Products distributed", "المنتجات الموزّعة"), v: TBC }
        ] }
      ],
      rel: ["media-siphat", "products-list", "partners-join"]
    },
    "partners-join": {
      sec: "partners", t: L("Become a Partner", "كن شريكاً"),
      lead: L("Tell us about your company and portfolio. Our business development team replies within two working days.", "حدّثنا عن شركتك ومحفظة منتجاتك، وسيرد عليك فريق تطوير الأعمال خلال يومَي عمل."),
      noCta: true,
      blocks: [
        { type: "list", h: L("What you get", "ما تحصل عليه"), items: [
          L("One partner for registration, pricing, promotion, storage and delivery", "شريك واحد للتسجيل والتسعير والترويج والتخزين والتوزيع"),
          L("Coverage of all 18 governorates from five strategic hubs", "تغطية المحافظات الـ18 كافة من خمسة مراكز استراتيجية"),
          L("GDP-certified storage, including 2 to 8 °C", "تخزين معتمد وفق GDP، بما في ذلك التخزين بين 2 و8 درجات مئوية"),
          L("Regular market and field reports", "تقارير دورية عن السوق والعمل الميداني")
        ] },
        { type: "form", kind: "partner" }
      ],
      rel: ["services-registration", "partners-list", "contact-office"]
    },

    /* ---------------- Products */
    "products-areas": {
      sec: "products", t: L("Therapeutic Areas", "المجالات العلاجية"),
      lead: L("Browse the portfolio by therapeutic area.", "تصفّح المحفظة حسب المجال العلاجي."),
      blocks: [ { type: "areas" },
        { type: "note", text: L("The list of areas will be confirmed against the registered product portfolio.", "ستُعتمد قائمة المجالات بعد مطابقتها مع محفظة المنتجات المسجّلة.") } ]
    },
    "products-list": {
      sec: "products", t: L("Product Listing", "قائمة المنتجات"),
      lead: L("Search and filter registered products by therapeutic area.", "ابحث في المنتجات المسجّلة وصنّفها حسب المجال العلاجي."),
      blocks: [ { type: "products" } ],
      rel: ["products-detail", "partners-list", "contact-inquiry"]
    },
    "products-detail": {
      sec: "products", t: L("Product Detail", "تفاصيل المنتج"),
      lead: L("Each product page shows only Ministry of Health approved information.", "تعرض صفحة كل منتج المعلومات المعتمدة من وزارة الصحة فقط."),
      blocks: [ { type: "productDetail" }, { type: "form", kind: "medical" } ],
      rel: ["products-areas", "partners-list", "contact-inquiry"]
    },

    /* ---------------- Media */
    "media-news": {
      sec: "media", t: L("News", "الأخبار"),
      lead: L("Company news and announcements.", "أخبار الشركة وإعلاناتها."),
      blocks: [ { type: "news" } ]
    },
    "media-siphat": {
      sec: "media", hidden: true, parent: "media-news", date: "2025-07",
      t: NEWS[0].t, lead: NEWS[0].d,
      blocks: [
        { type: "text", p: [
          L("Al-Qawsan Scientific Bureau and the Tunisian manufacturer SIPHAT signed a partnership agreement in July 2025. The signing took place under the supervision of Tunisia’s Minister of Health.",
            "وقّع مكتب القوسان العلمي والشركة التونسية المصنّعة SIPHAT اتفاقية شراكة في تموز 2025، وجرى التوقيع بإشراف وزير الصحة التونسي."),
          L("The agreement covers the export of Tunisian-made medicines to the Iraqi market and the transfer of pharmaceutical manufacturing technology.",
            "تشمل الاتفاقية تصدير الأدوية التونسية الصنع إلى السوق العراقية ونقل تكنولوجيا التصنيع الدوائي.")
        ] },
        { type: "source", text: L("Source: Tunisie Numérique, July 2025.", "المصدر: Tunisie Numérique، تموز 2025."),
          href: "https://news-tunisia.tunisienumerique.com/tunisia-and-iraq-join-forces-to-expand-pharmaceutical-exports-and-health-sovereignty/" }
      ],
      rel: ["partners-siphat", "media-news", "partners-join"]
    },
    "media-events": {
      sec: "media", t: L("Events & Conferences", "الفعاليات والمؤتمرات"),
      lead: L("Scientific meetings, CME sessions and conferences we organise or attend.", "اللقاءات العلمية وجلسات التعليم الطبي المستمر والمؤتمرات التي ننظمها أو نشارك فيها."),
      blocks: [ { type: "events" } ]
    },
    "media-gallery": {
      sec: "media", t: L("Photo & Video Gallery", "معرض الصور والفيديو"),
      lead: L("Our warehouses, fleet, teams and events.", "مستودعاتنا وأسطولنا وفرقنا وفعالياتنا."),
      blocks: [ { type: "gallery" } ]
    },

    /* ---------------- Careers */
    "careers-why": {
      sec: "careers", t: L("Why Join Us", "لماذا تنضم إلينا"),
      lead: L("Join more than 1,500 professionals building healthcare access across Iraq.", "انضم إلى أكثر من <bdi dir=\"ltr\">1,500</bdi> موظف ومختص يعملون على إيصال الرعاية الصحية في أنحاء العراق."),
      blocks: [
        { type: "features", items: [
          { icon: "users", t: L("Teamwork", "العمل الجماعي"), d: L("Regulatory, medical, sales and logistics teams that work as one.", "فرق تنظيمية وطبية ومبيعات ولوجستيات تعمل كفريق واحد.") },
          { icon: "chartUp", t: L("Continuous Development", "التطوير المستمر"), d: L("Regular scientific and compliance training.", "تدريب علمي وتدريب على الامتثال بشكل منتظم.") },
          { icon: "mapPin", t: L("National reach", "انتشار وطني"), d: L("Roles in Baghdad, Basra, Erbil, Mosul and Najaf.", "وظائف في بغداد والبصرة وأربيل والموصل والنجف.") },
          { icon: "shieldCheck", t: L("Responsibility", "المسؤولية"), d: L("Work that matters to patients and to the health system.", "عمل له أثر في المرضى والنظام الصحي.") }
        ] }
      ]
    },
    "careers-positions": {
      sec: "careers", t: L("Open Positions", "الوظائف الشاغرة"),
      lead: L("Current vacancies across our hubs.", "الوظائف المتاحة حالياً في مراكزنا."),
      blocks: [ { type: "jobs" } ]
    },
    "careers-apply": {
      sec: "careers", t: L("Apply Online", "قدّم طلبك"),
      lead: L("Send your CV. Human Resources reviews every application.", "أرسل سيرتك الذاتية، ويراجع فريق الموارد البشرية كل طلب."),
      blocks: [ { type: "form", kind: "apply" } ]
    },

    /* ---------------- Contact */
    "contact-office": {
      sec: "contact", t: L("Head Office", "المكتب الرئيسي"),
      lead: L("Visit or contact our head office in Baghdad.", "تفضّل بزيارة مكتبنا الرئيسي في بغداد أو التواصل معه."),
      blocks: [ { type: "office" } ]
    },
    "contact-branches": {
      sec: "contact", t: L("Branches & Coverage", "الفروع والتغطية"),
      lead: L("Five strategic hubs serve all 18 governorates.", "خمسة مراكز استراتيجية تخدم المحافظات الـ18 كافة."),
      blocks: [ { type: "map" }, { type: "hubs" } ]
    },
    "contact-inquiry": {
      sec: "contact", t: L("Inquiry Form", "نموذج الاستفسار"),
      lead: L("Send us a question. Sales and Customer Service reply within one working day.", "أرسل استفسارك، وسيرد عليك فريق المبيعات وخدمة العملاء خلال يوم عمل واحد."),
      noCta: true,
      blocks: [ { type: "form", kind: "inquiry" } ]
    },

    /* ---------------- Utility pages */
    "sitemap": {
      sec: null, t: L("Sitemap", "خريطة الموقع"),
      lead: L("Every page on the site, grouped by section.", "جميع صفحات الموقع مصنّفة حسب القسم."),
      blocks: [ { type: "sitemap" } ]
    },
    "privacy": {
      sec: null, t: L("Privacy Policy", "سياسة الخصوصية"),
      lead: L("How we handle the information you send us.", "كيف نتعامل مع المعلومات التي ترسلها إلينا."),
      blocks: [ { type: "text", p: [
        L("We use the details you send through our forms only to answer your request. We do not sell or share them with third parties.", "نستخدم البيانات التي ترسلها عبر نماذجنا للرد على طلبك فقط، ولا نبيعها ولا نشاركها مع أي طرف ثالث."),
        L("To ask us to correct or delete your details, use the inquiry form.", "لطلب تصحيح بياناتك أو حذفها، استخدم نموذج الاستفسار.")
      ] }, { type: "note", text: L("Final wording to be approved by legal counsel.", "تُعتمد الصيغة النهائية من المستشار القانوني.") } ]
    },
    "terms": {
      sec: null, t: L("Terms of Use", "شروط الاستخدام"),
      lead: L("The conditions for using this website.", "شروط استخدام هذا الموقع."),
      blocks: [ { type: "text", p: [
        L("Product information on this site is for healthcare professionals and follows Ministry of Health registration. It does not replace medical advice.", "معلومات المنتجات في هذا الموقع موجهة للكوادر الصحية وتتبع تسجيل وزارة الصحة، ولا تغني عن الاستشارة الطبية."),
        L("Content on this site belongs to Al-Qawsan Scientific Bureau unless stated otherwise.", "محتوى هذا الموقع ملك لمكتب القوسان العلمي ما لم يُذكر خلاف ذلك.")
      ] }, { type: "note", text: L("Final wording to be approved by legal counsel.", "تُعتمد الصيغة النهائية من المستشار القانوني.") } ]
    }
  };

  window.QS = {
    L: L, TBC: TBC, UI: UI, NAV: NAV, PAGES: PAGES, STATS: STATS, STATS_BANNER: STATS_BANNER,
    HUBS: HUBS, SERVICES: SERVICES, AREAS: AREAS, NEWS: NEWS, FORMS: FORMS, SLIDES: SLIDES,
    GROUP: [
      { t: L("CAS Development", "CAS Development"), d: L("Group company. Profile to be added.", "إحدى شركات المجموعة، وسيُضاف ملفها لاحقاً.") },
      { t: L("Lara Scientific Office", "مكتب لارا العلمي"), d: L("Scientific office, part of the group since 2015.", "مكتب علمي ضمن المجموعة منذ عام 2015.") },
      { t: L("Sanaya Scientific Office", "مكتب سنايا العلمي"), d: L("Scientific office, part of the group since 2019.", "مكتب علمي ضمن المجموعة منذ عام 2019.") }
    ]
  };
})();
