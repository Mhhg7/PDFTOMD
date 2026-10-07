#!/usr/bin/env python3
"""Builds deck.html: Modern Pharmaceutical Sales Strategies (Clinical Ledger theme)."""
from pathlib import Path

CSS = r"""
@font-face{font-family:'Fraunces';src:url('fonts/Fraunces[SOFT,WONK,opsz,wght].ttf');font-weight:100 900;font-style:normal}
@font-face{font-family:'Fraunces';src:url('fonts/Fraunces-Italic[SOFT,WONK,opsz,wght].ttf');font-weight:100 900;font-style:italic}
@font-face{font-family:'DM Sans';src:url('fonts/DMSans[opsz,wght].ttf');font-weight:100 1000}
@font-face{font-family:'DM Mono';src:url('fonts/DMMono-Regular.ttf');font-weight:400}
@font-face{font-family:'DM Mono';src:url('fonts/DMMono-Medium.ttf');font-weight:500}
@page{size:1920px 1080px;margin:0}
:root{--ink:#111B2D;--ink2:#1E2A3F;--paper:#F8F6F1;--card:#EFECE4;--text:#161E2B;--muted:#5C6678;--line:#D6D1C7;--amber:#E7A82A;--amberT:#9E6608;--blue:#286BD9;--blueT:#174DA8;--green:#1C8759;--greenT:#126B45;--coral:#D9614D;--coralT:#B2412F;--tA:#FCEFD5;--tB:#E2ECFC;--tG:#DDF1E7;--tC:#FCE4DF;--soft:#C9D3E3}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--paper)}
body{font-family:'DM Sans',sans-serif;color:var(--text);-webkit-font-smoothing:antialiased}
.slide{width:1920px;height:1080px;position:relative;overflow:hidden;background:var(--paper);break-after:page}
.slide.dark{background:var(--ink);color:#fff}
.serif{font-family:'Fraunces',serif;font-variation-settings:'opsz' 144,'SOFT' 50}
.mono{font-family:'DM Mono',monospace}
.kicker{position:absolute;left:120px;top:72px;font-weight:600;font-size:19px;letter-spacing:.14em;text-transform:uppercase}
.title{position:absolute;left:120px;top:108px;width:1680px;font-family:'Fraunces',serif;font-variation-settings:'opsz' 144,'SOFT' 50;font-weight:560;font-size:58px;line-height:1.08;letter-spacing:-.012em;color:var(--ink)}
.footer{position:absolute;left:120px;right:120px;bottom:44px;height:30px;display:flex;align-items:center;justify-content:space-between;font-size:17px;color:var(--muted)}
.dark .footer{color:var(--soft)}
.footer b{color:var(--text);font-weight:600}
.prog{display:flex;align-items:center;gap:14px;height:30px;padding:0 20px;position:relative}
.prog::before,.prog::after{content:'';position:absolute;top:0;bottom:0;width:8px;border:2px solid var(--muted)}
.prog::before{left:0;border-right:none}.prog::after{right:0;border-left:none}
.prog i{width:12px;height:12px;border-radius:50%;background:var(--line);display:block}
.prog i.on{background:var(--amber)}
.pg{font-variant-numeric:tabular-nums;letter-spacing:.06em;min-width:70px;text-align:right}
.brk{position:absolute}
.brk::before,.brk::after{content:'';position:absolute;top:0;bottom:0;width:var(--bw,34px);border:var(--bs,5px) solid var(--bc,var(--ink))}
.brk::before{left:0;border-right:none}.brk::after{right:0;border-left:none}
.disc{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-weight:600;font-size:26px;color:#fff;background:var(--ink);flex:none}
.chip{display:inline-flex;align-items:center;gap:8px;font-size:15px;font-weight:600;letter-spacing:.08em;text-transform:uppercase}
.chip b{width:10px;height:10px;border-radius:50%;display:block}
.pill{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:999px;font-size:14px;font-weight:600;letter-spacing:.06em;text-transform:uppercase}
.card{border-radius:26px;background:#fff}
.lbl{font-size:16px;font-weight:600;letter-spacing:.12em;text-transform:uppercase}
.q{font-family:'Fraunces',serif;font-style:italic;font-variation-settings:'opsz' 72,'SOFT' 100;font-weight:380}
.h3{font-family:'Fraunces',serif;font-variation-settings:'opsz' 72;font-weight:560;font-size:30px;line-height:1.15}
.body{font-size:21px;line-height:1.5}
.small{font-size:18px;line-height:1.45;color:var(--muted)}
.tip{position:relative;border-radius:22px;padding:24px 28px 26px 92px}
.tip .badge{position:absolute;left:26px;top:22px;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:22px}
.tip h4{font-size:21px;font-weight:650;margin:6px 0 6px}
.tip p{font-size:20px;line-height:1.5}
.note{position:absolute;display:flex;gap:18px;align-items:flex-start;padding:18px 24px;border-radius:18px;background:#fff;border:2px dashed var(--nc,var(--line))}
.note .tag{font-weight:700;font-size:13px;letter-spacing:.16em;color:#fff;background:var(--nt,var(--ink));padding:6px 10px;border-radius:7px;flex:none;margin-top:2px}
.note p{font-size:18.5px;line-height:1.45}
.row{display:flex;gap:22px;align-items:center}
.ph{font-family:'DM Mono',monospace;font-size:.9em;background:var(--tA);color:var(--amberT);padding:1px 7px;border-radius:5px}
"""

FAM = {1: 'amber', 2: 'blue', 3: 'amber', 4: 'green', 5: 'green', 6: 'green'}
slides = []


def add(body, dark=False):
    slides.append(f'<section class="slide{" dark" if dark else ""}">{body}</section>')


def head(kicker, title, color='amberT'):
    h = f'<div class="kicker" style="color:var(--{color})">{kicker}</div><div class="title">{title}</div>'
    return h


def foot(text='Modern Pharmaceutical Sales Strategies', on=()):
    dots = ''.join(f'<i class="{"on" if (on == "all" or k in on) else ""}"></i>' for k in range(1, 7))
    prog = f'<div class="prog">{dots}</div>' if on else ''
    return f'<div class="footer"><span>{text}</span><div class="row" style="gap:40px">{prog}<span class="pg"></span></div></div>'


def note(x, y, w, text, tone='ink', tag='NOTE'):
    nt = {'ink': 'var(--ink)', 'coral': 'var(--coralT)', 'blue': 'var(--blueT)', 'green': 'var(--greenT)', 'amber': 'var(--amberT)'}[tone]
    nc = {'ink': 'var(--line)', 'coral': 'var(--coral)', 'blue': 'var(--blue)', 'green': 'var(--green)', 'amber': 'var(--amber)'}[tone]
    return f'<div class="note" style="left:{x}px;top:{y}px;width:{w}px;--nt:{nt};--nc:{nc}"><span class="tag">{tag}</span><p>{text}</p></div>'


def tip(label, body, tone='amber', glyph='!', style=''):
    t = {'amber': ('tA', 'amber', 'amberT'), 'blue': ('tB', 'blue', 'blueT'), 'green': ('tG', 'green', 'greenT'), 'coral': ('tC', 'coral', 'coralT')}[tone]
    return f'<div class="tip" style="background:var(--{t[0]});{style}"><div class="badge" style="background:var(--{t[1]})">{glyph}</div><h4 style="color:var(--{t[2]})">{label}</h4>{f"<p>{body}</p>" if body else ""}</div>'


def deliv(text, n):
    return foot(f'Deliverable: <b>{text}</b>', on=(n,))


def divider(num, title, contents, color='amber'):
    body = f'''
<div style="position:absolute;left:120px;top:330px;width:1100px">
  <div class="lbl" style="color:var(--{color});font-size:20px;letter-spacing:.18em">{"Strategy " + num if num.isdigit() else num}</div>
  <div class="serif" style="font-size:96px;font-weight:560;line-height:1.02;letter-spacing:-.02em;margin-top:26px">{title}</div>
  <div style="margin-top:34px;font-size:26px;font-weight:300;color:var(--soft);line-height:1.5">{contents}</div>
</div>
<div class="brk" style="left:1340px;top:250px;width:460px;height:580px;--bc:var(--{color});--bs:9px;--bw:60px"></div>
<div class="serif" style="position:absolute;left:1340px;top:330px;width:460px;text-align:center;font-size:330px;line-height:1;font-weight:500">{num if num.isdigit() else "★"}</div>
''' + foot()
    add(body, dark=True)


# ---------------------------------------------------------------- 1 cover
add('''
<div style="position:absolute;left:120px;top:230px;width:860px">
  <div class="lbl" style="color:var(--amber);font-size:20px;letter-spacing:.18em">Field playbook</div>
  <div class="serif" style="margin-top:30px;font-size:110px;line-height:.98;font-weight:560;letter-spacing:-.025em">Modern Pharmaceutical Sales Strategies</div>
  <div style="margin-top:38px;font-size:30px;font-weight:300;color:var(--soft);line-height:1.45">Reverse negotiation · financial modelling · behavioural psychology</div>
  <div style="margin-top:44px;width:120px;height:6px;background:var(--amber)"></div>
  <div style="margin-top:30px;font-size:22px;color:var(--soft)">Six strategies &nbsp;·&nbsp; six institutional deliverables &nbsp;·&nbsp; one Product Battlecard</div>
</div>
<div class="brk" style="left:1110px;top:150px;width:690px;height:780px;--bc:var(--amber);--bs:9px;--bw:64px"></div>
<svg style="position:absolute;left:1110px;top:150px" width="690" height="780" viewBox="0 0 690 780">
 <g font-family="DM Sans" font-size="15" font-weight="600" letter-spacing="2.2" fill="#C9D3E3" text-anchor="middle">
  <g transform="translate(235,160)"><circle cx="-18" cy="0" r="46" fill="none" stroke="#E7A82A" stroke-width="5"/><circle cx="22" cy="10" r="46" fill="#E7A82A" fill-opacity=".22" stroke="#E7A82A" stroke-width="5"/><text y="96">01 NEGOTIATE</text></g>
  <g transform="translate(455,160)"><rect x="-52" y="10" width="24" height="40" fill="#286BD9" fill-opacity=".45"/><rect x="-14" y="-14" width="24" height="64" fill="#286BD9" fill-opacity=".7"/><rect x="24" y="-44" width="24" height="94" fill="#286BD9"/><line x1="-62" y1="52" x2="60" y2="52" stroke="#C9D3E3" stroke-width="3"/><text y="96">02 MODEL</text></g>
  <g transform="translate(235,375)"><g fill="none" stroke="#E7A82A" stroke-width="3"><rect x="-48" y="-48" width="28" height="28"/><rect x="-14" y="-48" width="28" height="28"/><rect x="20" y="-48" width="28" height="28"/><rect x="-48" y="-14" width="28" height="28"/><rect x="20" y="-14" width="28" height="28"/><rect x="-48" y="20" width="28" height="28"/><rect x="-14" y="20" width="28" height="28"/><rect x="20" y="20" width="28" height="28"/></g><rect x="-14" y="-14" width="28" height="28" fill="#E7A82A"/><text y="96">03 NUDGE</text></g>
  <g transform="translate(455,375)"><circle r="52" fill="none" stroke="#1C8759" stroke-width="5"/><path d="M0 0 L0 -52 A52 52 0 0 1 52 0 Z" fill="#1C8759" fill-opacity=".35"/><g stroke="#D9614D" stroke-width="5" stroke-linecap="round"><line x1="0" y1="-60" x2="0" y2="-70"/><line x1="60" y1="0" x2="70" y2="0"/><line x1="0" y1="60" x2="0" y2="70"/><line x1="-60" y1="0" x2="-70" y2="0"/></g><text y="96">04 SIMULATE</text></g>
  <g transform="translate(235,590)"><path d="M-6 -50 A50 50 0 0 0 -6 50 Z" fill="#D9614D"/><path d="M6 -50 A50 50 0 0 1 6 50 Z" fill="#286BD9"/><text y="96">05 RED TEAM</text></g>
  <g transform="translate(455,590)"><polygon points="0,-54 51,-17 32,44 -32,44 -51,-17" fill="none" stroke="#1C8759" stroke-width="3"/><polygon points="0,-27 26,-8 16,22 -16,22 -26,-8" fill="none" stroke="#1C8759" stroke-opacity=".5" stroke-width="2"/><polygon points="0,-46 40,-13 22,30 -26,36 -36,-12" fill="#1C8759" fill-opacity=".35" stroke="#1C8759" stroke-width="3"/><text y="96">06 REHEARSE</text></g>
 </g>
</svg>''' + foot(), dark=True)

# ---------------------------------------------------------------- 2 overview
cards = [
    (1, 'amber', 'tA', 'FBI<br>Negotiation', 'Lead the client to a “safe no”, then let calibrated questions do the work.', 'Psychology'),
    (2, 'blue', 'tB', 'Pharm&shy;onomics', 'Talk shelf return and cash flow with the pharmacist, not discounts.', 'Finance'),
    (3, 'amber', 'tA', 'Behavioural Nudge Matrix', 'Arrange the choice so the right option feels natural and low-risk.', 'Psychology'),
    (4, 'green', 'tG', 'Business War Games', 'Rehearse sudden market shocks in real time, under pressure.', 'Simulation'),
    (5, 'green', 'tG', 'Red<br>Teaming', 'Attack your own product before a competitor does.', 'Simulation'),
    (6, 'green', 'tG', 'AI Persona Simulator', 'Practise on a virtual physician and get scored on five dimensions.', 'Simulation'),
]
cs = ''.join(f'''<div class="card" style="background:var(--{t});padding:30px 26px;display:flex;flex-direction:column"><div class="disc" style="background:var(--{c}T)">{n}</div><div class="serif" style="font-size:30px;font-weight:560;line-height:1.12;margin-top:28px">{name}</div><div style="font-size:19px;line-height:1.5;margin-top:16px">{d}</div><div class="chip" style="margin-top:auto;color:var(--{c}T)"><b style="background:var(--{c})"></b>{fam}</div></div>''' for n, c, t, name, d, fam in cards)
add(head('Agenda', 'Six strategies, one shift: from pitching to proving') + f'''
<div style="position:absolute;right:120px;top:84px;display:flex;gap:30px">
 <span class="chip" style="color:var(--amberT)"><b style="background:var(--amber)"></b>Psychology</span>
 <span class="chip" style="color:var(--blueT)"><b style="background:var(--blue)"></b>Finance</span>
 <span class="chip" style="color:var(--greenT)"><b style="background:var(--green)"></b>Simulation</span></div>
<div class="brk" style="left:120px;top:250px;width:1680px;height:520px;--bw:44px"></div>
<div style="position:absolute;left:190px;top:280px;width:1540px;height:460px;display:grid;grid-template-columns:repeat(6,1fr);gap:20px">{cs}</div>
<div style="position:absolute;left:120px;top:800px;width:1680px;height:120px;border-radius:24px;background:var(--ink);color:#fff;display:flex;align-items:center;gap:30px;padding:0 40px">
 <span class="pill" style="background:var(--amber);color:var(--ink)">Capstone</span>
 <span class="serif" style="font-size:32px;font-weight:540">The Product Battlecard</span>
 <span style="margin-left:auto;font-size:20px;color:var(--soft)">One page that brings all six strategies together, updated every quarter</span></div>
''' + foot())

# ---------------------------------------------------------------- 3 the shift
pillars = [('amber', 'Reverse negotiation'), ('blue', 'Financial modelling'), ('amber', 'Behavioural psychology')]
moves = [
    ('amber', 'Tactical empathy &amp; accusation audits', 'disarm healthcare providers before the pitch begins'),
    ('blue', 'Portable Pharmonomics calculators', 'show pharmacists real inventory profitability and cash-flow gains'),
    ('green', 'AI persona simulators', 'replicate diverse physician personalities for training and performance evaluation'),
    ('green', 'Red teaming &amp; business war games', 'prepare teams to counter aggressive competition and sudden industry disruption'),
    ('amber', 'Behavioural nudge matrices &amp; micro-expression analysis', 'help reps navigate cognitive biases and guide decisions without direct friction'),
]
pl = ''.join(f'<div style="height:150px;border-radius:22px;background:var(--t{c[0].upper()});border-left:8px solid var(--{c});padding:0 30px;display:flex;align-items:center"><span class="h3">{p}</span></div>' for c, p in pillars)
mv = ''.join(f'<div class="row" style="background:#fff;border-radius:20px;padding:22px 28px;gap:24px"><span class="disc" style="width:44px;height:44px;font-size:20px;background:var(--{c}{"T" if c != "green" else "T"})">{i+1}</span><div><div style="font-size:23px;font-weight:650">{t}</div><div class="small" style="margin-top:4px">{d}</div></div></div>' for i, (c, t, d) in enumerate(moves))
add(head('Overview', 'Traditional marketing, rebuilt as an advanced discipline') + f'''
<div style="position:absolute;left:120px;top:262px;width:560px">
 <div class="lbl" style="color:var(--muted)">Built on three pillars</div>
 <div style="margin-top:20px;display:flex;flex-direction:column;gap:18px">{pl}</div>
</div>
<div style="position:absolute;left:740px;top:262px;width:1060px">
 <div class="lbl" style="color:var(--muted)">Five moves that replace the traditional pitch</div>
 <div style="margin-top:20px;display:flex;flex-direction:column;gap:14px">{mv}</div>
</div>''' + foot())

# ================================================================ FBI
divider('1', 'FBI Negotiation Methodology', 'Tactical empathy · accusation audit · calibrated open questions · the Negotiation Playbook', 'amber')

flow = ''
steps4 = [('Step 1', 'Accusation audit', 'Voice their objections first'), ('Step 2', 'A “safe no”', 'The client feels in control'), ('Step 3', 'Calibrated questions', 'Open “how” questions invite collaboration'), ('Outcome', 'They solve it', 'The client resolves the rep’s challenge themselves')]
for i, (k, t, d) in enumerate(steps4):
    last = i == 3
    flow += f'<div class="card" style="flex:1;padding:28px 30px;border:3px solid var(--amber);background:{"var(--amber)" if last else "#fff"}"><div class="lbl" style="color:{"var(--ink)" if last else "var(--amberT)"}">{k}</div><div class="h3" style="margin-top:10px">{t}</div><div style="font-size:19px;line-height:1.45;margin-top:10px;color:{"var(--ink)" if last else "var(--muted)"}">{d}</div></div>'
    if not last:
        flow += '<svg width="60" height="150" viewBox="0 0 60 150" style="flex:none;align-self:center"><path d="M18 48 L42 75 L18 102" fill="none" stroke="#9E6608" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
add(head('Strategy 1 · Concept', 'From persuasion to reverse negotiation and risk management') + f'''
<div style="position:absolute;left:120px;top:262px;width:1680px;display:grid;grid-template-columns:1fr 1fr;gap:30px">
 <div class="card" style="background:var(--card);padding:34px 38px;height:300px">
  <div class="lbl" style="color:var(--muted)">Traditional persuasion</div>
  <div class="body" style="margin-top:18px;color:var(--muted)">The rep pushes the pitch and tries to force the physician or pharmacist to accept it. Closed questions invite a defensive “yes” or “no”.</div>
 </div>
 <div class="card" style="background:var(--tA);padding:34px 38px;height:300px">
  <div class="lbl" style="color:var(--amberT)">Reverse negotiation · tactical empathy</div>
  <div class="body" style="margin-top:18px">The rep leads the client to a <b>“safe no”</b> that gives them a feeling of control. Calibrated questions then guide them to solve the rep’s challenge themselves.</div>
 </div>
</div>
<div style="position:absolute;left:120px;top:600px;width:1680px;display:flex">{flow}</div>
{note(120, 830, 1680, 'A “safe no” is not a lost visit. Giving the client control lowers their defences, which is what makes the calibrated questions that follow work.', 'amber')}
''' + foot(on=(1,)))

add(head('Strategy 1 · Technique', 'The accusation audit: say their objection before they do') + f'''
<div style="position:absolute;left:120px;top:262px;width:1080px;display:flex;flex-direction:column;gap:24px">
 <div class="card" style="background:var(--tA);padding:34px 40px">
  <div class="lbl" style="color:var(--amberT)">Lecture example · opening line</div>
  <div class="q" style="font-size:31px;line-height:1.38;margin-top:16px">“Doctor, you probably think I’m coming to repeat the same daily scientific pitch you hear all the time, and that our product is higher priced without justification.”</div>
 </div>
 <div class="card" style="background:#fff;padding:34px 40px;border:3px solid var(--amber)">
  <div class="lbl" style="color:var(--amberT)">Battlecard version · names the competitor</div>
  <div class="q" style="font-size:28px;line-height:1.4;margin-top:16px">“Doctor / Pharmacist, before we begin, you probably think I’m here to repeat the same standard sales pitch you hear every day, and that our product is priced higher without offering a true clinical advantage over <span class="ph" style="font-style:normal">[Competitor Brand]</span>.”</div>
 </div>
</div>
<div style="position:absolute;left:1260px;top:262px;width:540px">
 <div class="lbl" style="color:var(--muted)">How it works</div>
 <div style="margin-top:20px;display:flex;flex-direction:column;gap:16px">
  <div class="row" style="background:#fff;border-radius:18px;padding:20px 24px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amberT)">1</span><span style="font-size:20px">Anticipate the worst thoughts the client already holds</span></div>
  <div class="row" style="background:#fff;border-radius:18px;padding:20px 24px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amberT)">2</span><span style="font-size:20px">Say them first, at the opening of the visit</span></div>
  <div class="row" style="background:var(--tG);border-radius:18px;padding:20px 24px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--green)">✓</span><span style="font-size:20px"><b>Impact:</b> the client’s defensive posture is neutralised</span></div>
 </div>
</div>
{note(1260, 650, 540, 'Replace <span class="ph">[Competitor Brand]</span> before use, and keep the tone sincere: the audit only works if it sounds honest, not sarcastic.', 'amber')}
''' + foot(on=(1,)))

qs = [
    ('General', 'How can I support you in cases that do not respond to the first line of treatment?'),
    ('General', 'How can we overcome the price barrier with the patient without affecting treatment quality?'),
    ('Physicians · battlecard', 'How do you currently manage patient cases that fail to achieve target clinical endpoints on first-line therapy?'),
    ('Pharmacists · battlecard', 'How can we structure an order that maximises your shelf turnover rate without tying up your monthly cash flow?'),
]
qh = ''.join(f'<div style="background:#fff;border-radius:18px;border-left:6px solid var(--amber);padding:20px 26px"><div class="lbl" style="color:var(--amberT);font-size:13px">{k}</div><div style="font-size:21px;line-height:1.45;margin-top:8px">“<b style="color:var(--amberT)">How</b>{t[3:]}”</div></div>' for k, t in qs)
add(head('Strategy 1 · Technique', 'Calibrated open questions: let the client solve it') + f'''
<div style="position:absolute;left:120px;top:262px;width:520px">
 <div class="lbl" style="color:var(--muted)">Removed</div>
 <div style="margin-top:14px;padding:20px 26px;border-radius:18px;background:var(--card);font-size:21px;color:var(--muted);text-decoration:line-through;text-decoration-color:var(--coral);text-decoration-thickness:2px">Closed questions that end in “yes” or “no”</div>
 <div class="lbl" style="color:var(--muted);margin-top:40px">Replaced with</div>
 <div class="body" style="margin-top:14px">Reverse, open-ended <b>“how”</b> questions that invite the doctor or pharmacist to collaborate on the solution, and guide them to resolve the objection themselves.</div>
</div>
<div style="position:absolute;left:700px;top:262px;width:1100px;display:flex;flex-direction:column;gap:14px">{qh}</div>
{note(120, 700, 520, 'Each question starts with “how” and points at the client’s problem, never at the product.', 'amber')}
''' + foot(on=(1,)))

rows = [
    ('“You’ll repeat the same daily scientific pitch.”', 'Open with the accusation audit before presenting.'),
    ('“Your product is priced higher without justification.”', 'How can we overcome the price barrier with the patient without affecting treatment quality?'),
    ('Patients not responding to first-line treatment', 'How can I support you in cases that do not respond to the first line of treatment?'),
    ('Pharmacy cash flow tied up in stock', 'How can we structure an order that maximises your shelf turnover rate without tying up your monthly cash flow?'),
]
rh = ''
for i in range(10):
    if i < len(rows):
        o, q = rows[i]
        rh += f'<div style="display:grid;grid-template-columns:60px 560px 1fr;gap:20px;align-items:center;background:#fff;border-radius:14px;padding:0 22px;height:56px"><span class="mono" style="color:var(--amberT);font-weight:500">{i+1:02d}</span><span style="font-size:18px;font-weight:600">{o}</span><span style="font-size:18px">{q}</span></div>'
    else:
        rh += f'<div style="display:grid;grid-template-columns:60px 560px 1fr;gap:20px;align-items:center;border:2px dashed var(--line);border-radius:14px;padding:0 22px;height:56px"><span class="mono" style="color:var(--muted)">{i+1:02d}</span><span style="font-size:17px;color:var(--muted)">Objection to be mapped from field visits</span><span style="font-size:17px;color:var(--muted)">Calibrated question to be written</span></div>'
add(head('Strategy 1 · Deliverable', 'The FBI Negotiation Playbook') + f'''
<div style="position:absolute;left:120px;top:236px;width:1680px;display:grid;grid-template-columns:60px 560px 1fr;gap:20px;padding:0 22px" class="lbl"><span style="color:var(--muted)">#</span><span style="color:var(--muted)">Objection or admission</span><span style="color:var(--muted)">Paired calibrated question</span></div>
<div style="position:absolute;left:120px;top:270px;width:1680px;display:flex;flex-direction:column;gap:9px">{rh}</div>
{note(120, 920, 1680, 'The playbook maps the top 10 most complex market objections. Rows 1–4 come from this material; rows 5–10 must be filled in by the team.', 'amber', 'TO DO')}
''' + deliv('FBI Negotiation Playbook', 1))

# ================================================================ PHARMONOMICS
divider('2', 'Pharmonomics', 'GMROI vs simple margin · opportunity cost · the 3-input calculator · free cash flow proof', 'blue')

add(head('Strategy 2 · Concept', 'Shelf return, not simple margin', 'blueT') + f'''
<div class="card" style="position:absolute;left:120px;top:262px;width:860px;height:640px;padding:40px 46px">
 <div class="lbl" style="color:var(--blueT)">Gross Margin Return on Inventory</div>
 <div class="row" style="margin-top:30px;gap:24px">
  <span class="serif" style="font-size:46px;font-weight:560">GMROI</span><span style="font-size:40px;color:var(--muted)">=</span>
  <span style="display:inline-flex;flex-direction:column;align-items:center;font-size:30px;line-height:1.25"><span style="padding:0 12px 10px">Gross margin</span><span style="border-top:3px solid var(--ink);padding:10px 12px 0">Average inventory cost</span></span>
 </div>
 <div class="small" style="margin-top:20px">Profit earned for every unit of money tied up on the shelf.</div>
 <div style="margin-top:40px;display:flex;flex-direction:column;gap:30px">
  <div><div class="row" style="justify-content:space-between;font-size:20px"><span><b>Slow mover</b> &nbsp;30% margin · 2 turns a year</span><span class="mono">0.86</span></div><div style="margin-top:10px;height:32px;border-radius:8px;background:var(--card)"><div style="width:54%;height:100%;border-radius:8px;background:var(--soft)"></div></div></div>
  <div><div class="row" style="justify-content:space-between;font-size:20px"><span><b>Fast mover</b> &nbsp;20% margin · 6 turns a year</span><span class="mono" style="color:var(--blueT)">1.50</span></div><div style="margin-top:10px;height:32px;border-radius:8px;background:var(--card)"><div style="width:94%;height:100%;border-radius:8px;background:var(--blue)"></div></div></div>
 </div>
 <div class="small" style="position:absolute;left:46px;bottom:30px;font-size:15px">Illustrative figures · GMROI = margin × turns at cost ÷ (1 − margin)</div>
</div>
<div style="position:absolute;left:1040px;top:262px;width:760px">
 <div class="h3">Move the visit away from price</div>
 <div class="body" style="margin-top:16px">Pharmacy visits shift from basic discount discussions to <b>data-driven financial modelling and shelf investment analysis</b>. Product turnover drives the pharmacy’s bottom line and cash flow.</div>
 {tip('Opportunity cost', 'Every slow-turning competitor product on the shelf is money the pharmacy could be earning elsewhere. The rep makes that cost visible.', 'blue', '€', 'margin-top:34px')}
</div>
{note(1040, 790, 760, 'A lower margin can still win. The example shows a 20% product returning more per unit of stock than a 30% one, because it turns 3× faster.', 'blue')}
''' + foot(on=(2,)))

add(head('Strategy 2 · Evidence', 'Turnover speed: the number that frees working capital', 'blueT') + f'''
<div class="card" style="position:absolute;left:120px;top:262px;width:1680px;height:330px;padding:36px 44px">
 <div class="lbl" style="color:var(--muted)">One 60-day window on the shelf</div>
 <svg width="1592" height="220" viewBox="0 0 1592 220" style="margin-top:14px">
  <g font-family="DM Sans" font-size="18" fill="#5C6678">
   <text x="0" y="52" font-weight="600" fill="#161E2B">Competitor brand</text><text x="0" y="76">60 days per cycle</text>
   <rect x="260" y="30" width="1300" height="44" rx="10" fill="#C9D3E3"/><text x="910" y="59" text-anchor="middle" fill="#161E2B" font-weight="600">1 cycle</text>
   <text x="0" y="152" font-weight="600" fill="#161E2B">Our product</text><text x="0" y="176">15–20 days per cycle</text>
   <rect x="260" y="130" width="315" height="44" rx="10" fill="#286BD9"/><rect x="585" y="130" width="315" height="44" rx="10" fill="#286BD9"/><rect x="910" y="130" width="315" height="44" rx="10" fill="#286BD9"/><rect x="1235" y="130" width="325" height="44" rx="10" fill="#286BD9" fill-opacity=".45"/>
   <g fill="#fff" font-weight="600" text-anchor="middle"><text x="417" y="159">1</text><text x="742" y="159">2</text><text x="1067" y="159">3</text><text x="1397" y="159">4 at 15 days</text></g>
   <g font-family="DM Mono" font-size="15"><text x="260" y="210">day 0</text><text x="1560" y="210" text-anchor="end">day 60</text></g>
  </g>
 </svg>
</div>
<div style="position:absolute;left:120px;top:630px;width:1680px;display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
 <div class="card" style="padding:28px 32px;height:230px"><div class="lbl" style="color:var(--muted)">Gross margin %</div><div class="body" style="margin-top:12px">Competitor: <b>high</b> (deep discount). Ours: <b>standard</b>.</div><div class="small" style="margin-top:10px">The competitor looks cheaper at first.</div></div>
 <div class="card" style="padding:28px 32px;height:230px;background:var(--tB)"><div class="lbl" style="color:var(--blueT)">Turnover rate</div><div class="body" style="margin-top:12px">Competitor: <b>60 days</b> (slow). Ours: <b>15–20 days</b> (fast).</div><div class="small" style="margin-top:10px">Ours frees up working capital 3–4× faster.</div></div>
 <div class="card" style="padding:28px 32px;height:230px;background:var(--ink);color:#fff"><div class="lbl" style="color:var(--amber)">GMROI · cash flow</div><div class="body" style="margin-top:12px">Competitor: lower overall yield. Ours: <b>higher free cash flow</b>.</div><div class="small" style="margin-top:10px;color:var(--soft)">Reallocating 0.5 m of shelf raises monthly cash flow.</div></div>
</div>
{note(120, 895, 1680, 'This comparison is qualitative. Before quoting numbers to a pharmacist, replace it with that pharmacy’s own figures from the calculator.', 'blue')}
''' + foot(on=(2,)))

add(head('Strategy 2 · Tool', 'The Pharmonomics calculator: three inputs, one chart', 'blueT') + f'''
<div style="position:absolute;left:120px;top:262px;width:640px;display:flex;flex-direction:column;gap:18px">
 <div class="lbl" style="color:var(--muted)">Excel sheet or mobile app, used in front of the pharmacist</div>
 <div class="row" style="background:#fff;border-radius:20px;padding:24px 28px;border-left:6px solid var(--blue)"><span class="disc" style="background:var(--blueT)">1</span><div><div style="font-size:23px;font-weight:650">Daily pharmacy foot traffic</div></div></div>
 <div class="row" style="background:#fff;border-radius:20px;padding:24px 28px;border-left:6px solid var(--blue)"><span class="disc" style="background:var(--blueT)">2</span><div><div style="font-size:23px;font-weight:650">Product price</div></div></div>
 <div class="row" style="background:#fff;border-radius:20px;padding:24px 28px;border-left:6px solid var(--blue)"><span class="disc" style="background:var(--blueT)">3</span><div><div style="font-size:23px;font-weight:650">Expected monthly turnover rate</div></div></div>
</div>
<div style="position:absolute;left:820px;top:262px;width:380px;height:648px;border-radius:46px;background:var(--ink);padding:22px">
 <div style="width:100%;height:100%;border-radius:30px;background:#fff;padding:28px 26px;display:flex;flex-direction:column">
  <div class="lbl" style="color:var(--blueT);font-size:14px">Pharmonomics calculator</div>
  <div style="margin-top:20px;display:flex;flex-direction:column;gap:12px;font-size:16px;color:var(--muted)">
   <div style="border:2px solid var(--line);border-radius:12px;padding:10px 14px">1 · Daily foot traffic</div>
   <div style="border:2px solid var(--line);border-radius:12px;padding:10px 14px">2 · Product price</div>
   <div style="border:2px solid var(--line);border-radius:12px;padding:10px 14px">3 · Monthly turnover rate</div></div>
  <div class="lbl" style="margin-top:24px;color:var(--muted);font-size:13px">Monthly free cash flow</div>
  <svg width="288" height="210" viewBox="0 0 288 210" style="margin-top:12px"><line x1="0" y1="180" x2="288" y2="180" stroke="#D6D1C7" stroke-width="2"/><rect x="40" y="96" width="70" height="84" rx="6" fill="#C9D3E3"/><rect x="170" y="36" width="70" height="144" rx="6" fill="#286BD9"/><text x="75" y="202" font-family="DM Sans" font-size="13" fill="#5C6678" text-anchor="middle">today</text><text x="205" y="202" font-family="DM Sans" font-size="13" fill="#5C6678" text-anchor="middle">reallocated</text><text x="205" y="24" font-family="DM Mono" font-size="20" fill="#174DA8" text-anchor="middle">+$X</text></svg>
 </div>
</div>
<div style="position:absolute;left:1260px;top:262px;width:540px">
 <div class="lbl" style="color:var(--blueT)">The free cash flow proof</div>
 <div class="q" style="font-size:30px;line-height:1.4;margin-top:20px">“If you reallocate half a metre of shelf space to our product instead of a slower-moving competitor item, your monthly free cash flow will increase by $X.”</div>
</div>
{note(1260, 720, 540, '<span class="ph">$X</span> is calculated live from the pharmacy’s own three inputs. Never quote a fixed figure.', 'blue')}
''' + deliv('Portable financial calculator', 2) )

# ================================================================ NUDGE
divider('3', 'Behavioural Nudge Matrix', 'Status quo reframing · choice architecture and anchoring · micro-video analysis · the matrix', 'amber')

add(head('Strategy 3 · Concept', 'Make the right choice the path of least resistance') + '''
<div class="card" style="position:absolute;left:120px;top:262px;width:1000px;height:600px;padding:36px 40px">
 <svg width="920" height="440" viewBox="0 0 920 440">
  <g font-family="DM Sans" font-size="19" font-weight="600">
   <circle cx="40" cy="220" r="16" fill="#111B2D"/><text x="40" y="270" text-anchor="middle" fill="#5C6678">today</text>
   <path d="M56 210 C 170 60, 260 40, 330 110 S 430 40, 500 120 S 620 30, 700 110 S 820 70, 860 90" fill="none" stroke="#D9614D" stroke-width="6" stroke-dasharray="14 10" stroke-linecap="round"/>
   <g fill="#D9614D"><circle cx="330" cy="110" r="10"/><circle cx="500" cy="120" r="10"/><circle cx="700" cy="110" r="10"/></g>
   <text x="300" y="40" fill="#B2412F">Argument: friction at every step</text>
   <path d="M56 232 C 260 330, 560 330, 860 300" fill="none" stroke="#E7A82A" stroke-width="10" stroke-linecap="round"/>
   <text x="300" y="380" fill="#9E6608">Designed choice: natural, low-risk, optimal</text>
   <circle cx="880" cy="300" r="20" fill="#E7A82A"/><circle cx="880" cy="90" r="20" fill="none" stroke="#D9614D" stroke-width="5"/>
  </g>
 </svg>
</div>
<div style="position:absolute;left:1180px;top:262px;width:620px">
 <div class="h3">Choice architecture + cognitive biases</div>
 <div class="body" style="margin-top:16px">Instead of traditional persuasion, reps use behavioural economics to reframe options, so that prescribing or stocking the target product becomes the natural choice, without direct confrontation.</div>
 <div class="lbl" style="color:var(--muted);margin-top:36px">Three mechanisms</div>
 <div style="margin-top:14px;display:flex;flex-direction:column;gap:12px">
  <div class="row" style="background:var(--tA);border-radius:16px;padding:16px 22px"><span class="disc" style="width:36px;height:36px;font-size:16px;background:var(--amberT)">1</span><span style="font-size:20px">Reframing status quo bias</span></div>
  <div class="row" style="background:var(--tA);border-radius:16px;padding:16px 22px"><span class="disc" style="width:36px;height:36px;font-size:16px;background:var(--amberT)">2</span><span style="font-size:20px">Choice architecture &amp; anchoring</span></div>
  <div class="row" style="background:var(--tA);border-radius:16px;padding:16px 22px"><span class="disc" style="width:36px;height:36px;font-size:16px;background:var(--amberT)">3</span><span style="font-size:20px">Micro-video analysis</span></div>
 </div>
</div>''' + foot(on=(3,)))

add(head('Strategy 3 · Mechanism 1', 'Reframing status quo bias') + f'''
<div class="card" style="position:absolute;left:120px;top:262px;width:820px;height:420px;padding:40px 44px">
 <div class="lbl" style="color:var(--muted)">Loss aversion changes the question</div>
 <div style="margin-top:34px">
  <div class="row" style="justify-content:space-between;font-size:20px"><b>Staying on the older treatment</b><span style="color:var(--coralT)">framed as a risk</span></div>
  <div style="margin-top:10px;height:40px;border-radius:10px;background:var(--tC)"><div style="width:46%;height:100%;border-radius:10px;background:var(--coral)"></div></div>
  <div class="row" style="justify-content:space-between;font-size:20px;margin-top:34px"><b>Moving to the newer option</b><span style="color:var(--amberT)">framed as the safer path</span></div>
  <div style="margin-top:10px;height:40px;border-radius:10px;background:var(--amber)"></div>
 </div>
 <div class="small" style="margin-top:34px">The new product is not “just another alternative”: staying put becomes the decision that needs justifying.</div>
</div>
<div style="position:absolute;left:1000px;top:262px;width:800px">
 <div class="lbl" style="color:var(--muted)">How it works</div>
 <div class="body" style="margin-top:12px">Physicians often default to familiar competitor drugs out of habit.</div>
 <div class="lbl" style="color:var(--muted);margin-top:28px">Application</div>
 <div class="body" style="margin-top:12px">Continuing with the competitor’s older drug is reframed as the <b>risk of remaining with an outdated treatment option</b>.</div>
 <div class="card" style="background:var(--tA);padding:28px 32px;margin-top:28px"><div class="lbl" style="color:var(--amberT);font-size:13px">Battlecard clinical re-frame</div><div class="q" style="font-size:25px;line-height:1.4;margin-top:10px">“Staying on older therapy out of habit carries an unmanaged risk of treatment failure in non-responsive patient subsets.”</div></div>
</div>
{note(120, 720, 820, 'This is a clinical claim. It needs cited supporting data and medical or regulatory approval before any rep says it.', 'coral', 'COMPLIANCE')}
''' + foot(on=(3,)))

add(head('Strategy 3 · Mechanism 2', 'Choice architecture and anchoring') + f'''
<div class="card" style="position:absolute;left:120px;top:262px;width:1000px;height:600px;padding:40px">
 <div class="lbl" style="color:var(--muted)">Stock order options shown to the pharmacist</div>
 <div style="display:flex;align-items:flex-end;justify-content:center;gap:40px;margin-top:50px;height:380px">
  <div style="width:220px;height:240px;border:3px solid var(--line);border-radius:22px;display:flex;flex-direction:column;align-items:center;justify-content:center"><span class="serif" style="font-size:54px;color:var(--muted)">S</span><span class="small">Small order</span></div>
  <div style="width:280px;height:360px;background:var(--amber);border-radius:26px;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative"><span class="pill" style="position:absolute;top:-18px;background:var(--ink);color:#fff">Best value</span><span class="serif" style="font-size:80px;color:#fff">M</span><span style="font-size:19px;font-weight:600;color:var(--ink)">The real sales target</span></div>
  <div style="width:240px;height:290px;border:3px solid var(--line);border-radius:22px;display:flex;flex-direction:column;align-items:center;justify-content:center"><span class="serif" style="font-size:60px;color:var(--muted)">L</span><span class="small">Anchor: large order</span></div>
 </div>
</div>
<div style="position:absolute;left:1180px;top:262px;width:620px">
 <div class="lbl" style="color:var(--muted)">How it works</div>
 <div class="body" style="margin-top:12px">The way choices and packages are structured directly influences what people buy.</div>
 <div class="lbl" style="color:var(--muted);margin-top:28px">Application</div>
 <div class="body" style="margin-top:12px">Order packages are anchored so the <b>middle choice</b>, the company’s true sales goal, automatically appears to deliver the greatest savings and value.</div>
</div>
{note(1180, 700, 620, 'Pair this with the Pharmonomics calculator: show the middle package’s real cash-flow effect, so the value is genuine, not just framed.', 'blue', 'COMBINE')}
''' + foot(on=(3,)))

add(head('Strategy 3 · Mechanism 3', 'Micro-video analysis: reading split-second cues') + f'''
<div style="position:absolute;left:120px;top:262px;width:1680px;height:250px;border-radius:26px;background:var(--ink);padding:34px 40px">
 <svg width="1600" height="182" viewBox="0 0 1600 182">
  <g fill="#F8F6F1">{''.join(f'<rect x="{12+k*40}" y="0" width="16" height="10" rx="2"/><rect x="{12+k*40}" y="172" width="16" height="10" rx="2"/>' for k in range(40))}</g>
  <g>{''.join(f'<rect x="{10+k*200}" y="26" width="180" height="130" rx="8" fill="{c}"/>' for k, c in enumerate(["#1E2A3F", "#D9614D", "#1E2A3F", "#1E2A3F", "#E7A82A", "#1E2A3F", "#1C8759", "#1E2A3F"]))}</g>
  <g font-family="DM Sans" font-size="18" font-weight="600" fill="#fff" text-anchor="middle"><text x="300" y="98">skepticism</text><text x="900" y="98">hesitation</text><text x="1300" y="98">sudden interest</text></g>
 </svg>
</div>
<div style="position:absolute;left:120px;top:550px;width:1680px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
 <div class="card" style="padding:28px 30px;height:220px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amberT)">1</span><div style="font-size:22px;font-weight:650;margin-top:16px">Record role-plays</div><div class="small" style="margin-top:6px">Actors portray real physicians in simulated sales visits</div></div>
 <div class="card" style="padding:28px 30px;height:220px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amberT)">2</span><div style="font-size:22px;font-weight:650;margin-top:16px">Slow-motion review</div><div class="small" style="margin-top:6px">Frame-by-frame analysis of facial expressions</div></div>
 <div class="card" style="padding:28px 30px;height:220px"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amberT)">3</span><div style="font-size:22px;font-weight:650;margin-top:16px">Name the cue</div><div class="small" style="margin-top:6px">Hidden skepticism, doubt, hesitation or sudden engagement</div></div>
 <div class="card" style="padding:28px 30px;height:220px;background:var(--tA)"><span class="disc" style="width:40px;height:40px;font-size:18px;background:var(--amber)">4</span><div style="font-size:22px;font-weight:650;margin-top:16px">Apply in real visits</div><div class="small" style="margin-top:6px">Reps read subtle non-verbal signals and adjust in the moment</div></div>
</div>
{note(120, 800, 1680, 'Record actors in role-play only, never real clinic visits, and get every participant’s consent to be filmed.', 'coral', 'COMPLIANCE')}
''' + foot(on=(3,)))

def mrow(bias, sub, tech, desc, svg, clinic, pharm):
    dot = lambda on: f'<span style="width:80px;display:flex;justify-content:center"><b style="width:18px;height:18px;border-radius:50%;{"background:var(--amber)" if on else "border:2px solid var(--line)"}"></b></span>'
    return f'<div style="display:grid;grid-template-columns:330px 560px 1fr 170px;gap:30px;align-items:center;background:#fff;border-radius:22px;padding:22px 0 22px 28px;height:168px"><div><div class="serif" style="font-size:28px;font-weight:560">{bias}</div><div style="font-size:17px;color:var(--muted);margin-top:6px">{sub}</div></div><div style="font-size:19px;line-height:1.5"><b style="color:var(--amberT)">{tech}.</b> {desc}</div>{svg}<div style="display:flex;justify-content:center;gap:4px">{dot(clinic)}{dot(pharm)}</div></div>'

svg1 = '<svg width="300" height="120" viewBox="0 0 300 120"><text x="0" y="22" font-family="DM Sans" font-size="15" fill="#5C6678">stay</text><rect x="70" y="8" width="150" height="22" rx="5" fill="#FCE4DF"/><rect x="70" y="8" width="70" height="22" rx="5" fill="#D9614D"/><text x="230" y="25" font-family="DM Sans" font-size="15" fill="#B2412F">risk</text><text x="0" y="82" font-family="DM Sans" font-size="15" fill="#5C6678">switch</text><rect x="70" y="68" width="150" height="22" rx="5" fill="#E7A82A"/><text x="230" y="85" font-family="DM Sans" font-size="15" fill="#9E6608">gain</text></svg>'
svg2 = '<svg width="300" height="120" viewBox="0 0 300 120"><rect x="6" y="54" width="70" height="56" rx="8" fill="none" stroke="#D6D1C7" stroke-width="3"/><rect x="104" y="20" width="90" height="90" rx="10" fill="#E7A82A"/><rect x="222" y="34" width="76" height="76" rx="8" fill="none" stroke="#D6D1C7" stroke-width="3"/><text x="41" y="90" font-family="DM Sans" font-size="16" font-weight="600" fill="#5C6678" text-anchor="middle">S</text><text x="149" y="73" font-family="DM Sans" font-size="20" font-weight="700" fill="#fff" text-anchor="middle">M</text><text x="260" y="80" font-family="DM Sans" font-size="16" font-weight="600" fill="#5C6678" text-anchor="middle">L</text></svg>'
svg3 = '<svg width="300" height="120" viewBox="0 0 300 120"><rect x="0" y="22" width="300" height="76" rx="8" fill="#111B2D"/><g fill="#1E2A3F"><rect x="10" y="38" width="62" height="44" rx="3"/><rect x="82" y="38" width="62" height="44" rx="3"/><rect x="226" y="38" width="62" height="44" rx="3"/></g><rect x="154" y="38" width="62" height="44" rx="3" fill="#E7A82A"/><text x="185" y="15" font-family="DM Mono" font-size="13" fill="#9E6608" text-anchor="middle">0.2 s</text></svg>'
add(head('Strategy 3 · Deliverable', 'The Behavioural Nudge Matrix') + f'''
<div class="brk" style="left:120px;top:252px;width:1680px;height:650px;--bw:40px"></div>
<div style="position:absolute;left:190px;top:280px;width:1540px;display:grid;grid-template-columns:330px 560px 1fr 170px;gap:30px" class="lbl"><span style="color:var(--muted)">Bias</span><span style="color:var(--muted)">Technique</span><span style="color:var(--muted)">Visual cue</span><span style="color:var(--muted);display:flex;justify-content:center;gap:4px;font-size:13px"><i style="width:80px;text-align:center;font-style:normal">Clinic</i><i style="width:80px;text-align:center;font-style:normal">Pharmacy</i></span></div>
<div style="position:absolute;left:190px;top:326px;width:1540px;display:flex;flex-direction:column;gap:18px">
{mrow('Status quo bias', 'Defaulting to familiar treatments out of habit', 'Loss-aversion reframing', 'Present staying with the older competitor treatment as the risk of remaining with an outdated option.', svg1, True, False)}
{mrow('Anchoring', 'The first option shapes how the rest are judged', 'Choice architecture', 'Structure order packages so the middle package, the real target, reads as the best value.', svg2, False, True)}
{mrow('Hidden signals', 'Skepticism, hesitation or sudden interest', 'Micro-expression analysis', 'Review role-play recordings in slow motion to read split-second cues.', svg3, True, True)}
</div>
<div style="position:absolute;left:120px;top:920px;font-size:17px;color:var(--muted)">Guardrail: every frame must rest on approved, evidence-based product information and the local code of promotional practice.</div>
''' + deliv('Behavioural Nudge Matrix, mapping each bias to a framing technique for clinics and pharmacies', 3))

# ================================================================ WAR GAMES
divider('4', 'Business War Games', 'Crisis command room · real-time injections · 10-minute strategic execution · Market Disruption SOP', 'green')

add(head('Strategy 4 · Simulation', 'The crisis command room: an alert every 15 minutes', 'greenT') + '''
<div style="position:absolute;left:120px;top:236px;font-size:21px;color:var(--muted);width:1680px">Sales reps and product managers work in one room. Unannounced emergency alerts arrive on display screens or internal channels.</div>
<svg style="position:absolute;left:120px;top:300px" width="1680" height="190" viewBox="0 0 1680 190">
 <g font-family="DM Mono" font-size="18" fill="#5C6678" text-anchor="middle">
  <line x1="40" y1="100" x2="1640" y2="100" stroke="#111B2D" stroke-width="4"/>
  <g fill="#1C8759" fill-opacity=".85"><rect x="40" y="118" width="266.7" height="16" rx="8"/><rect x="440" y="118" width="266.7" height="16" rx="8"/><rect x="840" y="118" width="266.7" height="16" rx="8"/><rect x="1240" y="118" width="266.7" height="16" rx="8"/></g>
  <g fill="#D9614D"><path d="M40 62 L58 80 L40 98 L22 80 Z"/><path d="M440 62 L458 80 L440 98 L422 80 Z"/><path d="M840 62 L858 80 L840 98 L822 80 Z"/><path d="M1240 62 L1258 80 L1240 98 L1222 80 Z"/></g>
  <text x="40" y="44">00′</text><text x="440" y="44">15′</text><text x="840" y="44">30′</text><text x="1240" y="44">45′</text><text x="1640" y="44">60′</text>
  <line x1="1640" y1="88" x2="1640" y2="112" stroke="#111B2D" stroke-width="4"/>
  <text x="173" y="166" fill="#126B45" font-family="DM Sans" font-size="16" font-weight="600">10-minute response</text>
  <text x="373" y="166" fill="#5C6678" font-family="DM Sans" font-size="16">5-min buffer</text>
 </g>
</svg>
<div style="position:absolute;left:120px;top:520px;width:1680px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
 <div class="card" style="background:var(--tC);padding:28px 30px;height:230px"><div class="lbl" style="color:var(--coralT)">Alert · 00′ · regulator</div><div class="serif" style="font-size:27px;font-weight:540;line-height:1.25;margin-top:14px">Official price adjustment decreed by the regulatory authority</div></div>
 <div class="card" style="background:var(--tC);padding:28px 30px;height:230px"><div class="lbl" style="color:var(--coralT)">Alert · 15′ · market</div><div class="serif" style="font-size:27px;font-weight:540;line-height:1.25;margin-top:14px">An unofficial or parallel shipment enters at dumped prices</div></div>
 <div class="card" style="background:var(--tC);padding:28px 30px;height:230px"><div class="lbl" style="color:var(--coralT)">Alert · 30′ · KOL</div><div class="serif" style="font-size:27px;font-weight:540;line-height:1.25;margin-top:14px">A key opinion leader cancels an upcoming medical conference</div></div>
 <div class="card" style="background:#fff;border:3px dashed var(--line);padding:28px 30px;height:230px"><div class="lbl" style="color:var(--muted)">Alert · 45′</div><div class="serif" style="font-size:27px;font-weight:540;line-height:1.25;margin-top:14px;color:var(--muted)">Unannounced: the team never knows what comes next</div></div>
</div>
''' + note(120, 800, 1680, 'Alerts arrive every 15 minutes and each response is due within 10, so teams have only 5 minutes to regroup before the next shock.', 'green') + foot(on=(4,)))

add(head('Strategy 4 · Execution', 'Rapid strategic execution: ten minutes per crisis', 'greenT') + f'''
<svg style="position:absolute;left:120px;top:262px" width="520" height="520" viewBox="-260 -260 520 520">
 <circle r="220" fill="none" stroke="#DDF1E7" stroke-width="44"/>
 <path d="M0 -220 A220 220 0 0 1 190.5 110" fill="none" stroke="#1C8759" stroke-width="44"/>
 <path d="M190.5 110 A220 220 0 0 1 -190.5 110" fill="none" stroke="#1C8759" stroke-opacity=".7" stroke-width="44"/>
 <path d="M-190.5 110 A220 220 0 0 1 0 -220" fill="none" stroke="#1C8759" stroke-opacity=".45" stroke-width="44"/>
 <g stroke="#F8F6F1" stroke-width="6"><line x1="0" y1="-198" x2="0" y2="-242"/><line x1="171" y1="99" x2="210" y2="121"/><line x1="-171" y1="99" x2="-210" y2="121"/></g>
 <text y="10" text-anchor="middle" font-family="Fraunces" font-size="120" font-weight="560" fill="#111B2D">10′</text>
 <text y="64" text-anchor="middle" font-family="DM Sans" font-size="20" fill="#5C6678">from alert to directive</text>
</svg>
<div style="position:absolute;left:720px;top:262px;width:1080px;display:flex;flex-direction:column;gap:20px">
 <div class="row" style="background:#fff;border-radius:22px;padding:28px 32px;gap:26px"><span class="disc" style="background:var(--green)">1</span><div><div class="h3">Reallocate the marketing budget</div><div class="small" style="margin-top:6px">Shift spend on the fly to where the shock hits</div></div></div>
 <div class="row" style="background:#fff;border-radius:22px;padding:28px 32px;gap:26px"><span class="disc" style="background:var(--green)">2</span><div><div class="h3">Adjust commercial and scientific messaging</div><div class="small" style="margin-top:6px">Update what the field says to physicians and pharmacists</div></div></div>
 <div class="row" style="background:#fff;border-radius:22px;padding:28px 32px;gap:26px"><span class="disc" style="background:var(--green)">3</span><div><div class="h3">Issue new directives to the field</div><div class="small" style="margin-top:6px">Clear, actionable instructions for every sales team</div></div></div>
</div>
{note(720, 760, 1080, 'Use war games to stress-test the Product Battlecards: inject a sudden competitor price drop and check that the card’s counter-framing still holds.', 'green', 'COMBINE')}
''' + deliv('Market Disruption SOP, a standard rapid-response protocol for regulatory or competitive shocks', 4))

# ================================================================ RED TEAMING
divider('5', 'Red Teaming', 'Two opposing fronts · simulated competitor attacks · offensive defence · Product Battlecards', 'green')

add(head('Strategy 5 · Concept', 'Attack your own product before a competitor does', 'greenT') + '''
<div style="position:absolute;left:120px;top:236px;font-size:21px;color:var(--muted);width:1680px">Military and security planning, adapted to pharmaceutical sales: part of the team plays an aggressive competitor that exploits the product’s <b style="color:var(--text)">real</b> weaknesses.</div>
<div style="position:absolute;left:120px;top:310px;width:1680px;height:520px;display:grid;grid-template-columns:1fr 120px 1fr">
 <div class="card" style="background:var(--tC);padding:38px 42px">
  <div class="chip" style="color:var(--coralT)"><b style="background:var(--coral);width:14px;height:14px"></b>Red team · the competitor</div>
  <div class="serif" style="font-size:34px;font-weight:560;margin-top:18px;line-height:1.2">Virtual budget, full tactical freedom</div>
  <div style="margin-top:26px;display:flex;flex-direction:column;gap:14px;font-size:21px">
   <div class="row" style="gap:16px"><span style="width:28px;height:4px;background:var(--coral);border-radius:2px"></span>Slash prices</div>
   <div class="row" style="gap:16px"><span style="width:28px;height:4px;background:var(--coral);border-radius:2px"></span>Amplify concerns about side effects</div>
   <div class="row" style="gap:16px"><span style="width:28px;height:4px;background:var(--coral);border-radius:2px"></span>Offer aggressive incentives to pharmacies</div></div>
  <div style="margin-top:26px;font-size:19px;color:var(--coralT)">Goal: a complete plan to pull physicians away from the product.</div>
 </div>
 <div style="display:flex;align-items:center;justify-content:center"><div class="serif" style="width:84px;height:84px;border-radius:50%;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-style:italic">vs</div></div>
 <div class="card" style="background:var(--tB);padding:38px 42px">
  <div class="chip" style="color:var(--blueT)"><b style="background:var(--blue);width:14px;height:14px"></b>Blue team · the company</div>
  <div class="serif" style="font-size:34px;font-weight:560;margin-top:18px;line-height:1.2">Offensive defence</div>
  <div style="margin-top:26px;font-size:21px;line-height:1.55">No passive excuses or basic scientific justifications. Build <b style="color:var(--blueT)">counter-framing tactics</b> that actively reshape how physicians and pharmacists see the market.</div>
 </div>
</div>
''' + note(120, 860, 1680, 'Red-team tactics such as amplifying side-effect concerns are rehearsal material only. Reps answer safety questions in the field with approved safety information.', 'coral', 'COMPLIANCE') + foot(on=(5,)))

ws = [
    ('ink', '1', 'Divide into two fronts', 'Split participants into the Red Team (aggressive competitor) and the Blue Team (company sales force).'),
    ('coral', '2', 'Red Team attack presentation', 'With a virtual budget and full freedom, present to the whole room how to dismantle the product’s market share and pull physicians away.'),
    ('blue', '3', 'Blue Team offensive defence', 'Build an immediate counter-strategy that reshapes how doctors and pharmacists evaluate the market, not passive excuses.'),
    ('green', '4', 'Document as Product Battlecards', 'Compile every tested attack angle and counter-argument into a battlecard per product line, updated quarterly.'),
]
wh = ''.join(f'<div class="card" style="padding:0;overflow:hidden;display:flex;flex-direction:column"><div style="height:12px;background:var(--{c})"></div><div style="padding:30px 30px 28px;display:flex;flex-direction:column;flex:1"><span class="disc" style="background:var(--{c if c in ("ink",) else c + "T" if c != "coral" else "coralT"})">{n}</span><div class="h3" style="margin-top:22px">{t}</div><div style="font-size:20px;line-height:1.5;margin-top:18px">{d}</div></div></div>' for c, n, t, d in ws)
add(head('Strategy 5 · Workshop', 'Running a Red Teaming workshop in four steps', 'greenT') + f'''
<div style="position:absolute;left:120px;top:262px;width:1680px;height:560px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px">{wh}</div>
{note(120, 850, 1680, 'The Red Team must attack the product’s real weaknesses. Battlecards are only “pre-tested” if the attacks were honest.', 'ink')}
''' + foot(on=(5,)))

pairs = [
    ('Price slashes &amp; commercial incentives', 'Deep discount packages or volume rebates to pharmacies to tie up shelf space.', 'Pharmonomics counter-offer', '“A higher discount percentage on a slow-moving item ties up working capital; our product drives higher monthly free cash flow through faster inventory turnover (GMROI).”', 'blue'),
    ('Status quo anchoring', 'The competitor positions its older, established drug as the “safe default” for high-volume clinics.', 'Clinical re-framing', '“Staying on older therapy out of habit carries an unmanaged risk of treatment failure in non-responsive patient subsets.”', 'amber'),
    ('Safety &amp; side-effect concerns', 'Competitor reps highlight minor side-effect profiles or trial limitations to create doubt among prescribers.', 'Approved safety data', 'Counter-message to be written from approved safety information and trial data.', 'coral'),
]
ph = ''.join(f'<div style="display:grid;grid-template-columns:1fr 70px 1fr;align-items:stretch"><div class="card" style="background:var(--tC);padding:22px 28px"><div style="font-size:21px;font-weight:650;color:var(--coralT)">{a}</div><div style="font-size:18px;line-height:1.45;margin-top:6px">{ad}</div></div><svg width="70" height="100%" viewBox="0 0 70 100" preserveAspectRatio="none" style="align-self:center;height:40px"><path d="M14 20 L40 50 L14 80" fill="none" stroke="#111B2D" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/></svg><div class="card" style="background:{"var(--tB)" if c == "blue" else "var(--tA)" if c == "amber" else "#fff"};padding:22px 28px;{"border:3px dashed var(--coral)" if c == "coral" else ""}"><div style="font-size:21px;font-weight:650;color:var(--{c}T)">{d}</div><div style="font-size:18px;line-height:1.45;margin-top:6px;{"color:var(--muted)" if c == "coral" else ""}">{dd}</div></div></div>' for a, ad, d, dd, c in pairs)
add(head('Strategy 5 · Attack vs defence', 'Every attack vector gets a pre-tested counter-frame', 'greenT') + f'''
<div style="position:absolute;left:120px;top:236px;width:1680px;display:grid;grid-template-columns:1fr 70px 1fr" class="lbl"><span style="color:var(--coralT)">Red Team attack vector</span><span></span><span style="color:var(--blueT)">Blue Team offensive defence</span></div>
<div style="position:absolute;left:120px;top:280px;width:1680px;display:flex;flex-direction:column;gap:18px">{ph}</div>
{note(120, 880, 1680, 'The source material gives no counter for the side-effect attack. Write it from approved safety data before this card is used.', 'coral', 'GAP')}
''' + deliv('Product Battlecards, refreshed every quarter to reflect competitor shifts', 5))

# ================================================================ AI SIMULATOR
divider('6', 'AI Persona Simulator', 'Custom knowledge base · persona Mega-Prompts · live voice or text rehearsal · 5-dimension scorecard', 'green')

add(head('Strategy 6 · Setup', 'From search tool to interactive practice partner', 'greenT') + '''
<div class="card" style="position:absolute;left:120px;top:262px;width:800px;height:640px;padding:40px 44px">
 <div class="lbl" style="color:var(--greenT)">Custom knowledge base</div>
 <div class="small" style="margin-top:8px">Loaded into a custom AI agent (Custom GPT or Gemini agent)</div>
 <div style="margin-top:30px;position:relative;height:300px">
  <div class="card" style="position:absolute;left:60px;top:0;width:540px;height:110px;background:var(--tG);padding:24px 30px;font-size:22px;font-weight:600">Scientific literature</div>
  <div class="card" style="position:absolute;left:30px;top:95px;width:540px;height:110px;background:#CDE9DA;padding:24px 30px;font-size:22px;font-weight:600">Clinical papers</div>
  <div class="card" style="position:absolute;left:0;top:190px;width:540px;height:110px;background:var(--green);color:#fff;padding:24px 30px;font-size:22px;font-weight:600">Product brochures</div>
 </div>
 <div class="body" style="margin-top:40px">The AI answers from the company’s own material, so objections sound like a real, informed physician’s.</div>
</div>
<div style="position:absolute;left:980px;top:262px;width:820px">
 <div class="lbl" style="color:var(--greenT)">A Mega-Prompt assigns the persona</div>
 <div style="margin-top:18px;display:flex;flex-direction:column;gap:18px">
  <div class="card" style="padding:30px 34px;border-left:8px solid var(--green)"><div class="h3">The skeptical university professor</div><div class="small" style="margin-top:8px">Analytical, protective of time, values trial endpoints and real-world safety data over marketing claims</div></div>
  <div class="card" style="padding:30px 34px;border-left:8px solid var(--green)"><div class="h3">The high-volume clinic physician</div><div class="small" style="margin-top:8px">Time-pressured, impatient with long pitches, needs the point in seconds</div></div>
 </div>
</div>
''' + note(980, 700, 820, 'Load only approved company materials, and never enter patient-identifiable data into an AI tool.', 'coral', 'COMPLIANCE') + foot(on=(6,)))

dims = [('Scientific accuracy', 'Precision in citing study data and clinical endpoints'), ('Conciseness', 'Delivering key points succinctly'), ('Linking product to benefits', 'Turning pharmacological features into patient outcomes'), ('Time management', 'Efficiency within the brief visit window'), ('Tone of influence', 'Tactical empathy and professional authority')]
dh = ''.join(f'<div class="row" style="background:#fff;border-radius:16px;padding:16px 22px;gap:18px"><span class="disc" style="width:38px;height:38px;font-size:17px;background:var(--greenT)">{i+1}</span><div style="flex:1"><div class="row" style="justify-content:space-between"><span style="font-size:21px;font-weight:650">{e}</span></div><div class="small" style="margin-top:2px;font-size:17px">{d}</div></div></div>' for i, (e, d) in enumerate(dims))
add(head('Strategy 6 · Rehearsal', 'Rehearse live, then get scored on five dimensions', 'greenT') + f'''
<div style="position:absolute;left:120px;top:262px;width:900px">
 <div class="body">Before a visit, the rep rehearses by voice or text. The AI stays in character, raises realistic objections, questions scientific claims and shows time-pressured skepticism.</div>
 <div style="margin-top:24px;display:flex;flex-direction:column;gap:10px">{dh}</div>
</div>
<div class="card" style="position:absolute;left:1080px;top:262px;width:720px;height:660px;padding:30px">
 <div class="lbl" style="color:var(--greenT)">Pitch scorecard · 1–10</div>
 <svg width="660" height="590" viewBox="-330 -300 660 600">
  <g fill="none" stroke="#D6D1C7" stroke-width="2"><polygon points="0,-180 171.2,-55.6 105.8,145.6 -105.8,145.6 -171.2,-55.6"/><polygon points="0,-120 114.1,-37.1 70.5,97.1 -70.5,97.1 -114.1,-37.1"/><polygon points="0,-60 57.1,-18.5 35.3,48.5 -35.3,48.5 -57.1,-18.5"/><line x1="0" y1="0" x2="0" y2="-180"/><line x1="0" y1="0" x2="171.2" y2="-55.6"/><line x1="0" y1="0" x2="105.8" y2="145.6"/><line x1="0" y1="0" x2="-105.8" y2="145.6"/><line x1="0" y1="0" x2="-171.2" y2="-55.6"/></g>
  <polygon points="0,-144 102.7,-33.4 74.1,101.9 -52.9,72.8 -154.1,-50.1" fill="#1C8759" fill-opacity=".22" stroke="#1C8759" stroke-width="4" stroke-linejoin="round"/>
  <g fill="#1C8759"><circle cx="0" cy="-144" r="7"/><circle cx="102.7" cy="-33.4" r="7"/><circle cx="74.1" cy="101.9" r="7"/><circle cx="-52.9" cy="72.8" r="7"/><circle cx="-154.1" cy="-50.1" r="7"/></g>
  <g font-family="DM Sans" font-size="19" font-weight="600" fill="#161E2B"><text x="0" y="-206" text-anchor="middle">1 · Scientific accuracy</text><text x="186" y="-64">2 · Conciseness</text><text x="120" y="184" text-anchor="middle">3 · Product-to-benefit</text><text x="-120" y="184" text-anchor="middle">4 · Time management</text><text x="-186" y="-74" text-anchor="end">5 · Tone of</text><text x="-186" y="-50" text-anchor="end">influence</text></g>
  <text x="0" y="250" font-family="DM Sans" font-size="16" fill="#5C6678" text-anchor="middle">Illustrative scores</text>
 </svg>
</div>''' + foot(on=(6,)))

pblocks = [('var(--amber)', '#F2C46A', 'Role &amp; Persona Assignment:', "You are playing the role of Dr. [Doctor's Name], a Skeptical University Professor\nand Senior Consultant in Cardiology running a high-volume, time-constrained clinic."),
           ('var(--blue)', '#8FB4F2', 'Persona Traits &amp; Behavioral Rules:', "- You are highly analytical, deeply skeptical of standard sales pitches, and\n  protective of your time.\n- You value rigorous clinical trial endpoints, real-world safety data, and\n  mechanism-of-action details over marketing claims.\n- If I attempt a traditional, pushy sales pitch, react with skepticism or\n  disinterest. Only engage if I use tactical empathy, address potential\n  objections upfront, or ask calibrated open questions."),
           ('var(--green)', '#7FD1A8', 'Simulation Workflow:', "1. Begin in character with a brief, time-pressured greeting as I enter your office.\n2. Respond dynamically based on the clinical brochures and papers in your knowledge base.\n3. Raise realistic objections about side-effect profiles, pricing, or head-to-head\n   trial limitations.\n4. Remain strictly in character until I type the command: <span style=\"color:#fff;background:rgba(217,97,77,.6);padding:1px 7px;border-radius:4px\">EVALUATE_PITCH</span>"),
           ('var(--coral)', '#F0A090', 'Post-Simulation Evaluation Protocol:', "Once triggered by EVALUATE_PITCH, step out of character and score my pitch 1–10 on:\n1. Scientific Accuracy  2. Conciseness  3. Linking Product to Benefits\n4. Time Management  5. Tone of Influence, with brief actionable feedback for each.")]
pb = ''.join(f'<div style="border-left:6px solid {b};padding-left:22px;white-space:pre-wrap"><span style="color:{c}">{h}</span>\n{t}</div>' for b, c, h, t in pblocks)
add(head('Strategy 6 · Toolkit', 'Sample Mega-Prompt: the skeptical university professor', 'greenT') + f'''
<div class="mono" style="position:absolute;left:120px;top:250px;width:1180px;height:690px;border-radius:26px;background:var(--ink);padding:38px 40px 30px 30px;color:#E6EBF3"><div style="display:flex;flex-direction:column;gap:22px;font-size:16.5px;line-height:1.55">{pb}</div></div>
<div style="position:absolute;left:1350px;top:250px;width:450px">
 <div class="lbl" style="color:var(--muted)">Anatomy of the prompt</div>
 <div style="margin-top:18px;display:flex;flex-direction:column;gap:12px;font-size:20px">
  <div class="row" style="gap:14px"><span style="width:6px;height:28px;background:var(--amber);border-radius:3px"></span>Who the AI plays</div>
  <div class="row" style="gap:14px"><span style="width:6px;height:28px;background:var(--blue);border-radius:3px"></span>How the persona behaves</div>
  <div class="row" style="gap:14px"><span style="width:6px;height:28px;background:var(--green);border-radius:3px"></span>How the rehearsal runs</div>
  <div class="row" style="gap:14px"><span style="width:6px;height:28px;background:var(--coral);border-radius:3px"></span>How the pitch is scored</div></div>
 <div class="lbl" style="color:var(--muted);margin-top:44px">Before you start</div>
 <div style="margin-top:18px;display:flex;flex-direction:column;gap:14px;font-size:19px;line-height:1.45">
  <div class="row" style="gap:14px;align-items:flex-start"><span class="disc" style="width:34px;height:34px;font-size:16px;background:var(--greenT)">1</span>Load brochures and clinical studies into the knowledge base</div>
  <div class="row" style="gap:14px;align-items:flex-start"><span class="disc" style="width:34px;height:34px;font-size:16px;background:var(--greenT)">2</span>Replace <span class="ph">[Doctor’s Name]</span></div>
  <div class="row" style="gap:14px;align-items:flex-start"><span class="disc" style="width:34px;height:34px;font-size:16px;background:var(--greenT)">3</span>Rehearse right before the visit, then type EVALUATE_PITCH</div></div>
</div>''' + foot('Prompt variations are collected into the institutional Prompt Engineering Guide', on=(6,)))

add(head('Strategy 6 · Deliverable', 'A training system that keeps learning', 'greenT') + f'''
<div style="position:absolute;left:120px;top:262px;width:1680px;display:grid;grid-template-columns:1fr 1fr;gap:30px">
 <div class="card" style="padding:40px 44px;height:300px;background:var(--tG)"><div class="lbl" style="color:var(--greenT)">Institutional deliverable 1</div><div class="h3" style="margin-top:14px;font-size:36px">Prompt Engineering Guide for Pharma</div><div class="body" style="margin-top:14px">Every Mega-Prompt and its variations, collected and maintained as an internal standard.</div></div>
 <div class="card" style="padding:40px 44px;height:300px;background:var(--ink);color:#fff"><div class="lbl" style="color:var(--amber)">Institutional deliverable 2</div><div class="h3" style="margin-top:14px;font-size:36px">In-house training bot</div><div class="body" style="margin-top:14px;color:var(--soft)">A dedicated bot for continuous skill development, available before every clinic visit.</div></div>
</div>
<div class="mono" style="position:absolute;left:120px;top:610px;width:1680px;border-radius:24px;background:#fff;border:2px solid var(--line);padding:30px 36px;font-size:19px;line-height:1.7">
 <div class="lbl" style="font-family:'DM Sans';color:var(--greenT)">Battlecard integration tag · simulator command</div>
 <div style="margin-top:10px">Load <span class="ph">[Product_Battlecard_Q3]</span> into context. Act as a skeptical consultant echoing the Red Team’s side-effect and pricing objections. Require the rep to respond with Section 3 &amp; 4 tactics, then evaluate with the 5-dimension scorecard (<b>EVALUATE_PITCH</b>).</div>
</div>
''' + deliv('Prompt Engineering Guide for Pharma and an in-house training bot', 6))

# ================================================================ CAPSTONE: BATTLECARD
divider('Capstone', 'The Product Battlecard', 'How to build it in five steps · a full sample card · where every section comes from', 'amber')

bsteps = [
    ('1', 'Run an adversarial Red Teaming simulation', 'Split into Red and Blue teams, give the Red Team a virtual budget and full freedom, and map every attack vector it uses.', [('coral', 'Red teaming')]),
    ('2', 'Formulate the offensive defence', 'Counter-frame each attack. Example: a deep discount reframed from a “saving opportunity” into slow-moving inventory risk that ties up working capital.', [('coral', 'Red teaming'), ('blue', 'Pharmonomics')]),
    ('3', 'Assemble the core sections', 'One page: attack vectors, offensive-defence talking points, accusation audit openings, calibrated questions and a Pharmonomics table.', [('amber', 'FBI'), ('blue', 'Pharmonomics')]),
    ('4', 'Load it into the AI Persona Simulator', 'The AI plays a skeptical physician or pharmacist raising the card’s exact attacks; the rep practises and is scored on 5 dimensions.', [('green', 'AI simulator')]),
    ('5', 'Institutionalise quarterly updates', 'Refresh with competitor price changes, new clinical trial entries and evolving market conditions.', [('green', 'War games')]),
]
bs = ''.join(f'<div class="row" style="background:#fff;border-radius:20px;padding:22px 28px;gap:24px;align-items:flex-start"><span class="disc" style="background:var(--ink)">{n}</span><div style="flex:1"><div class="row" style="justify-content:space-between;gap:12px"><span style="font-size:23px;font-weight:650">{t}</span><span class="row" style="gap:8px">{"".join(f"<span class=pill style=background:var(--t{c[0].upper() if c != 'coral' else 'C'});color:var(--{c}T)>{l}</span>" for c, l in chips)}</span></div><div style="font-size:18.5px;line-height:1.45;color:var(--muted);margin-top:6px">{d}</div></div></div>' for n, t, d, chips in bsteps)
add(head('Capstone · Method', 'How to build a Product Battlecard in five steps') + f'''
<div style="position:absolute;left:120px;top:250px;width:1180px;display:flex;flex-direction:column;gap:14px">{bs}</div>
<div style="position:absolute;left:1360px;top:250px;width:440px">
 <div class="lbl" style="color:var(--muted)">One single page</div>
 <div class="card" style="margin-top:18px;height:560px;padding:24px;display:flex;flex-direction:column;gap:12px;box-shadow:0 1px 0 var(--line)">
  <div style="height:70px;border-radius:12px;background:var(--ink);color:#fff;padding:14px 18px;font-size:15px;font-weight:600">[Product] vs [Competitor] · Q3</div>
  <div style="flex:1.2;border-radius:12px;background:var(--tC);padding:12px 16px;font-size:14px;font-weight:600;color:var(--coralT)">1 · Attack vectors</div>
  <div style="flex:1;border-radius:12px;background:var(--tB);padding:12px 16px;font-size:14px;font-weight:600;color:var(--blueT)">2 · Offensive defence</div>
  <div style="flex:.7;border-radius:12px;background:var(--tA);padding:12px 16px;font-size:14px;font-weight:600;color:var(--amberT)">3 · Accusation audit</div>
  <div style="flex:.7;border-radius:12px;background:var(--tA);padding:12px 16px;font-size:14px;font-weight:600;color:var(--amberT)">4 · Calibrated questions</div>
  <div style="flex:1;border-radius:12px;background:var(--tB);padding:12px 16px;font-size:14px;font-weight:600;color:var(--blueT)">5 · Pharmonomics table</div>
  <div style="flex:.6;border-radius:12px;background:var(--tG);padding:12px 16px;font-size:14px;font-weight:600;color:var(--greenT)">6 · AI simulator tag</div>
 </div>
</div>''' + foot(on='all'))


def sechead(n, en, color, src):
    return f'<div class="row" style="gap:16px"><span class="disc" style="width:42px;height:42px;font-size:19px;background:var(--{color}T)">{n}</span><span style="font-size:23px;font-weight:650">{en}</span><span class="pill" style="margin-left:auto;background:var(--card);color:var(--muted);font-size:12px">from {src}</span></div>'


bc_top = '''<div style="position:absolute;left:120px;top:236px;width:1680px;height:110px;border-radius:24px 24px 0 0;background:var(--ink);color:#fff;display:flex;align-items:center;gap:30px;padding:0 36px">
 <span style="font-size:30px">🛡</span><div><div class="serif" style="font-size:32px;font-weight:560">Product Battlecard: <span class="ph" style="background:#2A3A55;color:#F2C46A">[Product Name]</span> vs. <span class="ph" style="background:#2A3A55;color:#F2C46A">[Competitor Brand]</span></div><div style="font-size:17px;color:var(--soft);margin-top:4px">Target line: Cardiovascular / Chronic line</div></div>
 <span class="pill" style="margin-left:auto;background:var(--amber);color:var(--ink)">Q3 update · refreshed quarterly</span>PAGE</div>'''

add(head('Capstone · Sample card 1/2', 'Sample battlecard: attacks, defence and the opening line') + bc_top.replace('PAGE', '<span class="mono" style="color:var(--soft);font-size:15px">1 / 2</span>') + f'''
<div style="position:absolute;left:120px;top:346px;width:1680px;height:600px;border-radius:0 0 24px 24px;background:#fff;padding:24px 36px;display:flex;flex-direction:column;gap:18px">
 <div style="background:var(--card);border-radius:14px;padding:12px 20px;font-size:19px"><b>Primary objective:</b> neutralise price-cutting and side-effect doubts, and shift commercial discussions to clinical value and shelf efficiency.</div>
 {sechead(1, 'Red Team attack vectors', 'coral', 'Red teaming')}
 <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
  <div style="background:var(--tC);border-radius:14px;padding:16px 20px"><div style="font-size:19px;font-weight:650;color:var(--coralT)">Price slashes &amp; incentives</div><div style="font-size:16.5px;line-height:1.4;margin-top:4px">Deep discount packages or volume rebates to pharmacies to tie up shelf space</div></div>
  <div style="background:var(--tC);border-radius:14px;padding:16px 20px"><div style="font-size:19px;font-weight:650;color:var(--coralT)">Safety &amp; side-effect concerns</div><div style="font-size:16.5px;line-height:1.4;margin-top:4px">Minor side-effect profiles or trial limitations highlighted to create doubt</div></div>
  <div style="background:var(--tC);border-radius:14px;padding:16px 20px"><div style="font-size:19px;font-weight:650;color:var(--coralT)">Status quo anchoring</div><div style="font-size:16.5px;line-height:1.4;margin-top:4px">Older, established drug positioned as the “safe default” for high-volume clinics</div></div>
 </div>
 {sechead(2, 'Blue Team offensive defence', 'blue', 'Red teaming · Nudge · Pharmonomics')}
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <div style="background:var(--tA);border-radius:14px;padding:16px 20px"><div class="lbl" style="font-size:12px;color:var(--amberT)">Clinical re-framing</div><div class="q" style="font-size:19px;line-height:1.4;margin-top:4px">“Staying on older therapy out of habit carries an unmanaged risk of treatment failure in non-responsive patient subsets.”</div></div>
  <div style="background:var(--tB);border-radius:14px;padding:16px 20px"><div class="lbl" style="font-size:12px;color:var(--blueT)">Pharmonomics counter-offer</div><div class="q" style="font-size:19px;line-height:1.4;margin-top:4px">“A higher discount on a slow-moving item ties up working capital; our product drives higher monthly free cash flow through faster turnover (GMROI).”</div></div>
 </div>
 {sechead(3, 'Accusation audit opening', 'amber', 'FBI negotiation')}
 <div class="q" style="font-size:20px;line-height:1.4;padding:0 6px">“Doctor / Pharmacist, before we begin, you probably think I’m here to repeat the same standard sales pitch you hear every day, and that our product is priced higher without offering a true clinical advantage over [Competitor Brand].”</div>
</div>''' + foot(on='all'))

add(head('Capstone · Sample card 2/2', 'Sample battlecard: questions, numbers and rehearsal') + bc_top.replace('PAGE', '<span class="mono" style="color:var(--soft);font-size:15px">2 / 2</span>') + f'''
<div style="position:absolute;left:120px;top:346px;width:1680px;height:600px;border-radius:0 0 24px 24px;background:#fff;padding:26px 36px;display:flex;flex-direction:column;gap:18px">
 {sechead(4, 'Calibrated open questions', 'amber', 'FBI negotiation')}
 <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <div style="border-left:6px solid var(--amber);background:var(--paper);border-radius:12px;padding:14px 20px"><div class="lbl" style="font-size:12px;color:var(--amberT)">For physicians</div><div style="font-size:18.5px;line-height:1.4;margin-top:4px">“How do you currently manage patient cases that fail to achieve target clinical endpoints on first-line therapy?”</div></div>
  <div style="border-left:6px solid var(--amber);background:var(--paper);border-radius:12px;padding:14px 20px"><div class="lbl" style="font-size:12px;color:var(--amberT)">For pharmacists</div><div style="font-size:18.5px;line-height:1.4;margin-top:4px">“How can we structure an order that maximises your shelf turnover rate without tying up your monthly cash flow?”</div></div>
 </div>
 {sechead(5, 'Pharmonomics metric comparison', 'blue', 'Pharmonomics')}
 <div style="display:grid;grid-template-columns:240px 1fr 1fr 1.4fr;font-size:17.5px;border-radius:12px;overflow:hidden;border:2px solid var(--line)">
  <div class="lbl" style="font-size:12px;background:var(--card);padding:10px 16px">Metric</div><div class="lbl" style="font-size:12px;background:var(--card);padding:10px 16px">Competitor brand</div><div class="lbl" style="font-size:12px;background:var(--card);padding:10px 16px;color:var(--blueT)">Our product</div><div class="lbl" style="font-size:12px;background:var(--card);padding:10px 16px">Strategic advantage</div>
  <div style="padding:10px 16px;font-weight:600">Gross margin %</div><div style="padding:10px 16px">High (deep discount)</div><div style="padding:10px 16px">Standard</div><div style="padding:10px 16px">Competitor appears cheaper initially</div>
  <div style="padding:10px 16px;font-weight:600;background:var(--paper)">Turnover rate</div><div style="padding:10px 16px;background:var(--paper)">60 days (slow)</div><div style="padding:10px 16px;background:var(--paper);color:var(--blueT);font-weight:600">15–20 days (fast)</div><div style="padding:10px 16px;background:var(--paper)">Frees up working capital</div>
  <div style="padding:10px 16px;font-weight:600">GMROI / cash flow</div><div style="padding:10px 16px">Lower overall yield</div><div style="padding:10px 16px;color:var(--blueT);font-weight:600">Higher free cash flow</div><div style="padding:10px 16px">0.5 m of shelf raises monthly cash flow</div>
 </div>
 {sechead(6, 'AI simulator integration tag', 'green', 'AI Persona Simulator')}
 <div class="mono" style="background:var(--ink);color:#E6EBF3;border-radius:12px;padding:14px 20px;font-size:16px;line-height:1.55">Load [Product_Battlecard_Q3] into context · act as a skeptical consultant echoing the Red Team’s side-effect and pricing objections · rep answers with Sections 3 &amp; 4 · evaluate with <span style="color:#F2C46A">EVALUATE_PITCH</span></div>
</div>''' + foot(on='all'))

strats = ['FBI', 'Pharmonomics', 'Nudge', 'War games', 'Red team', 'AI sim']
scol = ['amber', 'blue', 'amber', 'green', 'green', 'green']
mapping = [
    ('1 · Red Team attack vectors', [0, 0, 1, 0, 2, 0]),
    ('2 · Blue Team offensive defence', [0, 2, 2, 0, 2, 0]),
    ('3 · Accusation audit opening', [2, 0, 0, 0, 0, 0]),
    ('4 · Calibrated open questions', [2, 0, 0, 0, 0, 0]),
    ('5 · Pharmonomics comparison', [0, 2, 0, 0, 0, 0]),
    ('6 · AI simulator tag', [0, 0, 0, 0, 0, 2]),
    ('Quarterly stress-test', [0, 0, 0, 2, 1, 1]),
]
def cell(v, c):
    if v == 2:
        return f'<span style="display:flex;justify-content:center"><b style="width:30px;height:30px;border-radius:50%;background:var(--{c});display:block"></b></span>'
    if v == 1:
        return f'<span style="display:flex;justify-content:center"><b style="width:30px;height:30px;border-radius:50%;border:4px solid var(--{c});display:block"></b></span>'
    return '<span style="display:flex;justify-content:center"><b style="width:10px;height:10px;border-radius:50%;background:var(--line);display:block"></b></span>'
mh = ''.join(f'<div style="display:grid;grid-template-columns:440px repeat(6,1fr);align-items:center;height:76px;background:#fff;border-radius:16px;padding:0 24px"><span style="font-size:21px;font-weight:600">{r}</span>{"".join(cell(v, scol[k]) for k, v in enumerate(vals))}</div>' for r, vals in mapping)
add(head('Capstone · Synthesis', 'Where each battlecard section comes from') + f'''
<div class="brk" style="left:120px;top:250px;width:1680px;height:690px;--bw:40px"></div>
<div style="position:absolute;left:180px;top:270px;width:1560px;display:grid;grid-template-columns:440px repeat(6,1fr);padding:0 24px" class="lbl"><span style="color:var(--muted)">Battlecard section</span>{"".join(f'<span style="text-align:center;color:var(--{c}T)">{s}</span>' for s, c in zip(strats, scol))}</div>
<div style="position:absolute;left:180px;top:316px;width:1560px;display:flex;flex-direction:column;gap:10px">{mh}</div>
<div style="position:absolute;right:120px;top:196px;display:flex;gap:30px;font-size:17px;color:var(--muted)"><span class="row" style="gap:10px"><b style="width:18px;height:18px;border-radius:50%;background:var(--ink);display:block"></b>main source</span><span class="row" style="gap:10px"><b style="width:18px;height:18px;border-radius:50%;border:3px solid var(--ink);display:block"></b>supporting</span></div>
''' + foot(on='all'))

# ================================================================ NOTES
divider('Notes', 'Field notes before the card goes live', 'What must be filled in · what must be checked · what must be approved', 'coral')

fill = [
    ('[Product Name] · [Competitor Brand]', 'Battlecard header, accusation audit, simulator tag'),
    ('[Doctor’s Name]', 'Mega-Prompt persona'),
    ('$X', 'Calculated live from each pharmacy’s three inputs, never a fixed figure'),
    ('[Product_Battlecard_Q3]', 'Rename for each quarter’s version'),
    ('Pharmonomics table', 'Replace the qualitative values with real figures'),
    ('Side-effect counter-message', 'Missing from the source; write it from approved safety data'),
    ('Playbook rows 5–10', 'Map the remaining objections from field visits'),
]
fh = ''.join(f'<div style="display:grid;grid-template-columns:44px 470px 1fr;align-items:center;gap:20px;background:#fff;border-radius:16px;padding:16px 24px"><b style="width:26px;height:26px;border-radius:7px;border:3px solid var(--amber);display:block"></b><span class="mono" style="font-size:18px;color:var(--amberT);font-weight:500">{a}</span><span style="font-size:19px">{b}</span></div>' for a, b in fill)
add(head('Field notes · 1 of 2', 'Fill in before use', 'amberT') + f'''
<div style="position:absolute;left:120px;top:250px;width:1680px;display:flex;flex-direction:column;gap:12px">{fh}</div>''' + foot('Every placeholder in this deck is marked like <span class="ph">[this]</span>'))

checks = [
    ('coral', 'Evidence for clinical claims', 'Statements such as “unmanaged risk of treatment failure in non-responsive patient subsets” need cited data and medical or regulatory approval.'),
    ('coral', 'Red Team ≠ field messaging', 'Amplifying side-effect concerns is a rehearsal tactic. In the field, safety questions are answered only with approved safety information.'),
    ('coral', 'Local promotion code', 'Nudges, anchoring and framing must stay within the local code of promotional practice and the product’s approved information.'),
    ('blue', 'Numbers from the pharmacy', 'GMROI and cash-flow claims come from the pharmacy’s own inputs; the illustrative figures in this deck are not for quoting.'),
    ('green', 'AI tools and data', 'Load only approved company materials into the simulator, and never enter patient-identifiable data.'),
    ('green', 'Consent for recordings', 'Micro-video analysis uses actors in role-play; every participant consents to being filmed.'),
    ('green', 'Quarterly ownership', 'Name an owner and a date for each quarterly battlecard update: competitor price changes, new trial entries, market shifts.'),
    ('amber', 'Calibrated question form', 'Keep every question open and starting with “how”; a yes/no question breaks the method.'),
]
ch = ''.join(f'<div class="card" style="padding:22px 26px;border-top:6px solid var(--{c})"><div style="font-size:20px;font-weight:650;color:var(--{c}T)">{t}</div><div style="font-size:17.5px;line-height:1.45;margin-top:8px">{d}</div></div>' for c, t, d in checks)
add(head('Field notes · 2 of 2', 'Check and approve before use', 'coralT') + f'''
<div style="position:absolute;left:120px;top:250px;width:1680px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px">{ch}</div>
<div style="position:absolute;left:120px;top:880px;display:flex;gap:30px;font-size:17px;color:var(--muted)"><span class="row" style="gap:10px"><b style="width:16px;height:16px;border-radius:4px;background:var(--coral);display:block"></b>compliance</span><span class="row" style="gap:10px"><b style="width:16px;height:16px;border-radius:4px;background:var(--blue);display:block"></b>numbers</span><span class="row" style="gap:10px"><b style="width:16px;height:16px;border-radius:4px;background:var(--green);display:block"></b>governance</span><span class="row" style="gap:10px"><b style="width:16px;height:16px;border-radius:4px;background:var(--amber);display:block"></b>method</span></div>
''' + foot())

# ================================================================ SUMMARY
summ = [('amber', '1', 'FBI Negotiation', 'Negotiation Playbook: top 10 objections with calibrated questions'), ('blue', '2', 'Pharmonomics', 'Portable financial calculator for GMROI and free cash flow'), ('amber', '3', 'Behavioural Nudge Matrix', 'Bias-to-technique matrix for clinics and pharmacies'), ('green', '4', 'Business War Games', 'Market Disruption SOP for rapid response'), ('green', '5', 'Red Teaming', 'Product Battlecards, updated quarterly'), ('green', '6', 'AI Persona Simulator', 'Prompt Engineering Guide for Pharma and an in-house training bot')]
sh = ''.join(f'<div style="display:grid;grid-template-columns:70px 480px 1fr;align-items:center;gap:26px;height:84px;background:#fff;border-radius:18px;padding:0 30px;border-left:8px solid var(--{c})"><span class="disc" style="width:46px;height:46px;font-size:21px;background:var(--{c}T)">{n}</span><span class="serif" style="font-size:27px;font-weight:560">{t}</span><span style="font-size:21px">{d}</span></div>' for c, n, t, d in summ)
sh += '<div style="display:grid;grid-template-columns:70px 480px 1fr;align-items:center;gap:26px;height:84px;background:var(--ink);color:#fff;border-radius:18px;padding:0 30px;border-left:8px solid var(--amber)"><span class="disc" style="width:46px;height:46px;font-size:21px;background:var(--amber);color:var(--ink)">★</span><span class="serif" style="font-size:27px;font-weight:560">Capstone · Product Battlecard</span><span style="font-size:21px;color:var(--soft)">One page that combines all six, rehearsed in the simulator, updated quarterly</span></div>'
add(head('Summary', 'Six strategies, six deliverables, one battlecard') + f'''
<div class="brk" style="left:120px;top:246px;width:1680px;height:700px;--bw:40px"></div>
<div style="position:absolute;left:186px;top:266px;width:1548px;display:flex;flex-direction:column;gap:10px">{sh}</div>''' + foot(on='all'))

add('''<div class="brk" style="left:120px;top:300px;width:1680px;height:480px;--bc:var(--amber);--bs:9px;--bw:64px"></div>
<div style="position:absolute;left:0;right:0;top:400px;text-align:center">
 <div class="serif" style="font-size:120px;font-weight:560;letter-spacing:-.025em;line-height:1">From pitching to proving.</div>
 <div style="margin-top:44px;font-size:28px;font-weight:300;color:var(--soft)">Empathy that disarms &nbsp;·&nbsp; numbers that convince &nbsp;·&nbsp; rehearsal that prepares</div>
</div>''' + foot(), dark=True)

SCRIPT = "<script>const s=[...document.querySelectorAll('section.slide')];s.forEach((e,i)=>{const p=e.querySelector('.pg');if(p)p.textContent=String(i+1).padStart(2,'0')+' / '+s.length;});</script>"
html = f'<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Modern Pharmaceutical Sales Strategies</title><style>{CSS}</style></head><body>\n' + '\n'.join(slides) + SCRIPT + '</body></html>'
Path(__file__).with_name('deck.html').write_text(html, encoding='utf-8')
print(len(slides), 'slides')
