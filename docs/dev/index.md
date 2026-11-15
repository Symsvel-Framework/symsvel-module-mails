# Mails : intégration Settings

Le module Mails déclare ses paramètres applicatifs auprès de Settings via `MailsSettingDefinitionProvider`, implémentation de `SettingDefinitionProviderInterface`. `MailsBundle::loadExtension()` enregistre explicitement le fournisseur comme service Symfony autoconfiguré ; l'interface porte le tag `symsvel.settings.definition_provider`.

L'inventaire Composer de Tooling associe ce fournisseur au module `mails`. Le registre Settings qualifie les clés courtes :

- `sender_email` → `mails.sender_email` (type `email`)
- `sender_name` → `mails.sender_name` (type `string`)
- `transport` → `mails.transport` (type `string`)

Ces paramètres sont privés, non secrets, facultatifs et non bloquants pour l'installation. Le transport est pour l'instant un identifiant déclaratif, sans instanciation de service d'envoi ni validation d'une liste de transports.

Le module dépend de `symsvel-framework/module-settings`. Il ne possède aucune table ni migration. Le stockage, les valeurs et leur éventuel chiffrement restent sous la responsabilité de Settings.

## Validation

Le test unitaire vérifie les trois définitions. La découverte effective du fournisseur, l'attribution de l'espace de noms `mails` et la persistance doivent encore être validées dans un projet SymSvel installé, avec Settings et Mails actifs. Aucun envoi d'e-mail n'est implémenté à ce stade.

## Dépendance de développement

L'interface `SettingDefinitionProviderInterface` est actuellement disponible sur la branche `dev` de Settings mais pas dans sa release `0.1.0`. Mails dépend donc explicitement de `symsvel-framework/module-settings:dev-dev` pendant ce chantier. Ne pas élargir à `^0.1 || dev-dev` : Composer peut sélectionner `0.1.0`, qui ne contient pas le contrat requis. Cette contrainte sera réévaluée lors de la publication d'une version de Settings intégrant l'interface.

### Visibilité des tests ignorés

La CI affiche les raisons des tests PHPUnit ignorés avec `--display-skipped` (directement ou via le script Composer QA). Un test ignoré ne devient pas bloquant pour autant : les motifs sont examinés pour distinguer prérequis absents et couverture manquante.

## Vérification de l'intégration Settings

Le test `MailsSettingDefinitionProviderTest::testRealProviderProducesCanonicalValueFreeMailsInventory` utilise le vrai `MailsSettingDefinitionProvider`, le registre Settings et `SettingKeyQualifier` avec le propriétaire vérifié `mails`. Il vérifie la production des clés `mails.sender_email`, `mails.sender_name`, `mails.transport`, leurs métadonnées et l'absence de valeurs dans l'inventaire. Ce test ne démarre pas Symfony et ne teste pas encore la synchronisation en base via Composer : ce parcours complet reste à valider sur le projet crash-test.

## Authentification des dépendances privées en CI

La QA du module Mails installe `symsvel-framework/module-settings` depuis le dépôt GitHub privé. Le workflow `.github/workflows/qa.yml` lit le secret d'organisation `SYMSVEL_READ_TOKEN` et transmet temporairement ses droits à Composer via la variable d'environnement `COMPOSER_AUTH` (`github-oauth.github.com`). Aucun jeton n'est écrit dans `composer.json` ni dans un fichier versionné. Le secret doit être accessible au dépôt Mails et autorisé à lire Settings ; si ce n'est pas le cas, l'installation échoue explicitement. Le résultat de la CI reste à confirmer.

## Découverte des paramètres Settings

`MailsBundle::loadExtension()` enregistre `MailsSettingDefinitionProvider` avec le tag explicite `symsvel.settings.definition_provider`. L'autoconfiguration reste activée mais ne constitue pas l'unique mécanisme d'enregistrement. Le compiler pass du module Settings associe ce provider au module Composer vérifié, puis `symsvel:settings:sync` publie les métadonnées dans `.symsvel/modules-params.json` et crée les entrées BDD sans initialiser de valeurs. Le test `MailsBundleTest::testRegistersTaggedSettingsProvider` protège cette déclaration. Vérifier en crash-test réel sur XT après installation/mise à jour : trois définitions Mails dans l'inventaire et en BDD ; CI et crash-test restent à confirmer.

## Registre de templates transactionnels (chantier Auth v1)

Le module Mails fournit désormais `MailTemplate`, `MailTemplateProviderInterface`, `MailTemplateRegistry`, `RegisteredMailRenderer` et `RenderedMail`, adaptés du socle XCore sans personnalisation. Chaque module déclare ses templates via un service implémentant `MailTemplateProviderInterface` et le tag `symsvel.mails.template_provider`. Les identifiants doivent être uniques et inclure le contexte, par exemple `auth.bo.password_reset` et `auth.app.password_reset`.

Chaque déclaration associe une langue à trois chemins Twig (`subject`, `html`, `text`) et une liste exhaustive de variables. Les chemins doivent être enregistrés dans le chargeur Twig du projet. Exemple d'arborescence dans un module : `mail/password-reset/fr/{subject.twig,body.html.twig,body.txt.twig}`, puis `en/` ; les chemins déclarés doivent correspondre aux noms Twig réellement accessibles. Le rendu choisit la langue demandée ou la langue de repli `fr`, vérifie les variables et produit un objet immuable. Les variables HTML sont échappées par Twig ; les URL de réinitialisation doivent être construites et validées par Auth, jamais par le moteur de rendu.

**Limites actuelles :** aucun transport ni envoi n'est encore câblé, aucun template Auth n'est encore déclaré et aucune migration BDD n'est requise pour les templates natifs. Le service Twig du backend et le chargement des chemins des modules doivent être vérifiés dans le projet installé. Les personnalisations XCore (BDD, contrôleur et éditeur BO) restent volontairement hors périmètre. Mailpit sera utilisé pour les CT SMTP locaux, indépendamment du futur transport Brevo. Tests unitaires ajoutés ; CI et CT restent à confirmer.

## Envoi SMTP (passe suivante)

`MailSender` reçoit un `RenderedMail`, une adresse destinataire et l'identité expéditrice. Il crée un `Symfony\\Component\\Mime\\Email` avec sujet, corps HTML et corps texte puis délègue à `MailerInterface`. Le module ne choisit ni n'instancie de transport : Symfony Mailer utilise `MAILER_DSN` du backend. Les adresses sont validées avant l'appel au transport ; un test unitaire couvre la composition du message et le rejet d'une adresse invalide.

Pour les tests locaux avec Mailpit : `MAILER_DSN=smtp://127.0.0.1:1025`. Les valeurs expéditeur pourront provenir des paramètres `mails.sender_email` et `mails.sender_name` déclarés dans Settings ; cette lecture n'est pas encore raccordée au service d'envoi. L'absence de transport configuré ne doit pas être masquée. Le transport Brevo reste hors périmètre. Aucun endpoint Auth ni template Auth n'est encore branché. La CI et les CT doivent être confirmés avant validation.

## Correctif de dépendance CI

Le module déclare explicitement `symfony/mailer:^8.1` dans ses dépendances Composer, en plus de Twig. Les tests unitaires de `MailSender` sont exécutés dans le dépôt Mails isolé, où la présence de Symfony Mailer dans le squelette Back ne suffit pas. Ce correctif résout la classe `Symfony\\Component\\Mailer\\MailerInterface` absente lors du chargement des tests ; la nouvelle CI doit encore confirmer l'exécution complète.

## Expéditeur transactionnel depuis Settings (15/11/2026)

`ConfiguredMailSender` est un adaptateur de `MailSender` qui lit à chaque envoi les clés qualifiées `mails.sender_email` et `mails.sender_name` via `SettingManager`. L'absence d'adresse expéditrice valide déclenche une `LogicException` avant tout appel au transport ; aucune adresse de repli n'est inventée. `MailsBundle` enregistre ce service, sans modifier `MailSender::send()` ni le choix du transport `MAILER_DSN`. Les tests vérifient la transmission des adresses et du nom, ainsi que l'absence d'envoi si la configuration est incomplète. CI et crash-test Mailpit à confirmer. Le module Auth doit encore raccorder ce service au déclenchement de récupération et appliquer les protections anti-abus.

### Correctif des tests de l'expéditeur configuré (15/11/2026)

`SettingManager` est `final` et ne peut pas être doublé par PHPUnit. Les deux tests de `ConfiguredMailSender` instancient désormais le vrai `SettingManager` et le vrai registre qualifié des définitions Mails, avec un `SettingRepository` simulé et des entités `Setting` en mémoire. Aucun changement du contrat ni du code de production. Correction à confirmer en CI.
