@extends('layout.mainlayout')
@section('title', 'Política de Privacitat :: JOK.cat')

@section('content')

<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-neutral-800 mb-6 font-['Comfortaa']">Política de Privacitat</h1>

    <div class="prose prose-neutral max-w-none text-sm text-neutral-700 space-y-6">

        <h2 class="text-lg font-semibold text-neutral-800">1. Informació que recollim</h2>
        <p>Podem recollir els següents tipus d'informació quan utilitzes la nostra aplicació:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong>Informació proporcionada per l'usuari:</strong> Nom, correu electrònic o altres dades que introdueixis voluntàriament.</li>
            <li><strong>Informació tècnica:</strong> Adreça IP, tipus de dispositiu, sistema operatiu i dades d'ús per millorar el rendiment de l'aplicació.</li>
        </ul>

        <h2 class="text-lg font-semibold text-neutral-800">2. Ús de la informació</h2>
        <p>Utilitzem la informació recollida per a:</p>  
        <ul class="list-disc pl-5 space-y-1">
            <li>Personalitzar l'experiència de l'usuari.</li>
            <li>Resoldre problemes tècnics i oferir suport.</li>
            <li>Complir amb les lleis i regulacions aplicables.</li>
        </ul>

        <h2 class="text-lg font-semibold text-neutral-800">3. Compartició de la informació</h2>
        <p>No venem, lloguem ni compartim informació personal amb tercers.</p>

        <h2 class="text-lg font-semibold text-neutral-800">4. Drets de l'usuari</h2>
        <p>Tens dret a:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Accedir, modificar o eliminar les teves dades personals.</li>
            <li>Retirar el consentiment per al tractament de dades en qualsevol moment.</li>
            <li>Contactar-nos per qualsevol dubte o sol·licitud relacionada amb la teva privadesa.</li>
        </ul>
        <p>Per exercir aquests drets, pots escriure'ns a <a href="mailto:jok@jok.cat" class="text-indigo-600 hover:underline">jok@jok.cat</a>.</p>

        <h2 class="text-lg font-semibold text-neutral-800">5. Seguretat de les dades</h2>
        <p>Adoptem mesures de seguretat adequades per protegir les dades dels usuaris contra accessos no autoritzats, pèrdues o alteracions.</p>

        <h2 class="text-lg font-semibold text-neutral-800">6. Canvis en aquesta política</h2>
        <p>Podem actualitzar aquesta política de privadesa en qualsevol moment. Notificarem els usuaris mitjançant una actualització dins l'aplicació o per altres mitjans adequats.</p>

        <h2 class="text-lg font-semibold text-neutral-800">7. Ús del correu electrònic als Anuncis (Segona Mà)</h2>
        <p>A la secció d'Anuncis de Segona Mà, el correu electrònic <strong>s'utilitza únicament com a identificador d'usuari</strong> per tal que els interessats en un anunci puguin contactar amb el venedor.</p>
        <p>El correu electrònic <strong>només s'utilitzarà</strong> en el següent àmbit:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Perquè un usuari registrat pugui publicar un anunci.</li>
            <li>Perquè un usuari interessat pugui enviar un correu al venedor d'un anunci.</li>
            <li>Per tal que el venedor rebin el correu de l'interessat i puguin contactar directament.</li>
        </ul>
        <p><strong>Només s'utilitzarà</strong> amb l'autorització explícita de l'usuari (mitjançant l'acceptació del checkbox de conformitat en publicar o contactar).</p>
        <p>El correu electrònic no es compartirà, vendrà ni s'utilitzarà per a finalitats de màrqueting.</p>

        <div class="mt-8 pt-6 border-t border-neutral-200">
            <p class="text-neutral-600">Si tens preguntes sobre aquesta política, contacta'ns a <a href="mailto:jok@jok.cat" class="text-indigo-600 hover:underline">jok@jok.cat</a>.</p>
            <p class="text-neutral-500 mt-4"><strong>Jok.cat</strong><br>jok@jok.cat</p>
        </div>

    </div>
</div>

@endsection