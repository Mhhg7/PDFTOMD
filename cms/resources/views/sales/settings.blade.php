@extends('sales.layout', ['title' => 'Sheet settings'])
@section('content')
<div class="adm-head"><div><h1>Order sheet settings</h1><p class="adm-muted">The footer of every order sheet, and the currency for prices.</p></div></div>
<form method="post" action="{{ route('sales.settings.update') }}" class="adm-card adm-form adm-narrow">
  @csrf
  <div class="qs-field"><label class="qs-field__label" for="s-web">Website</label><input class="qs-input" id="s-web" name="website" value="{{ old('website', $s['website']) }}" dir="ltr" maxlength="120"></div>
  <div class="qs-field"><label class="qs-field__label" for="s-addr">Address</label><input class="qs-input" id="s-addr" name="address" value="{{ old('address', $s['address']) }}" maxlength="160"></div>
  <div class="qs-field"><label class="qs-field__label" for="s-phone">Phone</label><input class="qs-input" id="s-phone" name="phone" value="{{ old('phone', $s['phone']) }}" dir="ltr" maxlength="60"></div>
  <div class="qs-field"><label class="qs-field__label" for="s-qr">QR code link</label><input class="qs-input" id="s-qr" name="qr_url" value="{{ old('qr_url', $s['qr_url']) }}" dir="ltr" placeholder="https://…"><p class="qs-field__hint">The QR code in the footer opens this address, for example the website or a WhatsApp link. Leave empty to show the empty QR box.</p></div>
  <div class="qs-field"><label class="qs-field__label" for="s-cur">Currency</label><input class="qs-input adm-num" id="s-cur" name="currency" value="{{ old('currency', $s['currency']) }}" required maxlength="10" dir="ltr"></div>
  <div class="adm-formfoot"><button class="qs-btn qs-btn--primary" type="submit"><span>Save settings</span></button></div>
</form>
@endsection
