<!DOCTYPE html>
<html>

<head>
    <title>Rapport des comptes désactivés du mois</title>
</head>

<body>
    <h1>Rapport des comptes désactivés du mois</h1>
    <p>Voici la liste des comptes désactivés ce mois-ci :</p>
    <ul>
        @foreach ($deactivatedAccounts as $account)
        <li>Compte ID: {{ $account->id }}, Nom: {{ $account->name }}, Prénom: {{ $account->surname }}, Date de
            désactivation: {{ $account->updated_at }}</li>
        @endforeach
    </ul>
</body>

</html>