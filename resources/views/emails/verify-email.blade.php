<x-mail::message>
# Witaj {{ $userName }}! 👋

Dziękujemy za rejestrację w naszym blogu. Aby móc w pełni korzystać z serwisu, potwierdź swój adres email klikając poniższy przycisk.

<x-mail::button :url="$verificationUrl" color="success">
Zweryfikuj adres email
</x-mail::button>

## Dlaczego to ważne?

Weryfikacja adresu email pozwala nam:
- Zapewnić bezpieczeństwo Twojego konta
- Wysyłać Ci powiadomienia o nowych komentarzach
- Potwierdzić, że jesteś prawdziwym użytkownikiem

<x-mail::panel>
⚠️ Link weryfikacyjny jest ważny przez **60 minut**. Po tym czasie będziesz mógł poprosić o nowy link z ustawień swojego profilu.
</x-mail::panel>

Jeśli nie zakładałeś konta w naszym serwisie, zignoruj tę wiadomość.

Pozdrawiamy,<br>
{{ config('app.name') }}

<x-mail::subcopy>
Jeśli masz problem z kliknięciem przycisku "Zweryfikuj adres email", skopiuj i wklej poniższy link do przeglądarki:
[{{ $verificationUrl }}]({{ $verificationUrl }})
</x-mail::subcopy>
</x-mail::message>
