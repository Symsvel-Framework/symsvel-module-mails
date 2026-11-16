# Mails : administration

Le module Mails fournit dans Settings :
- Adresse e-mail expéditrice.
- Nom affiché de l'expéditeur.
- **Transport actif**, au moyen d'un menu déroulant.

Les choix natifs sont **SMTP personnalisé**, **Mailpit**, **MailHog**,
**smtp4dev** et **Mailtrap**. Une seule option est active à la fois.
Il s'agit de propositions statiques : SymSvel ne recherche pas les
services installés sur le poste, ne les installe pas et ne vérifie pas
qu'ils sont démarrés. Le développeur configure et démarre lui-même son
serveur SMTP, et renseigne `MAILER_DSN` ou `MAILTRAP_DSN` si besoin.

Lorsqu'un serveur sélectionné ne répond pas, Symfony Mailer renvoie
l'erreur normale de livraison. Aucun envoi n'est basculé vers un
autre transport sans l'accord de l'administrateur.

Les options d'autres fournisseurs sont ajoutées au menu uniquement
par leurs modules de transport effectivement installés, sans sonder
les services externes. Les comptes fournisseurs et secrets de transport
ne sont jamais exposés dans le dropdown.

Pour un CT local, lancer soi-même Mailpit, choisir **Mailpit** dans
Settings et définir l'expéditeur. Le port local par défaut est
`127.0.0.1:1025`, configurable dans Symfony.
