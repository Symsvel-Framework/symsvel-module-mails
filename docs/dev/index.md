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
