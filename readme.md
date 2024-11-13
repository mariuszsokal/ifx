# IFX

## Wymagania
Proszę napisać próbkę kodu zgodną z poniższymi wymaganiami biznesowymi oraz 
- PHP w wersji 8.*
- Framework agnostic
- DomainDrivenDesign (zwracamy uwagę na strukturę kodu oraz powiązania między jego poszczególnymi elmentami)
- Całość przetestowana testami jednostkowymi (przypadki testowe powinny pokrywać wszystkie scenariusze)

“Konto bankowe i płatność”:

Konto bankowe:
- konto ma swoją walutę
- na konto można przyjmować pieniądze (credit) oraz wysyłać z niego pieniądze (debit) tylko w takiej samej walucie jaką ma konto
- Konto ma swój balans wynikający z wykonanych na nim operacji credit i debit
- Każda płatność wychodząca (debit) musi być powiększona o koszty transakcji 0,5%
- z konta bankowego można wysłać pieniądze tylko jeżeli kwota płatności (powiększona o koszt transakcji) mieści się w dostępnym balansie
- Konto bankowe zezwala na zrobienie maksymalnie 3 płatności wychodzących 1 dnia

Płatność:
- Zawiera kwotę oraz walutę

## Ważne informacje
Kwoty przedstawione są w groszach co by uniknąć problemów z liczeniem liczb zmiennoprzecinkowych.

## Struktura Katalogów

- `src/Domain`: Klasy agregatów, encji itd.
- `src/Domain/ValueObject`: Klasy ValueObject.
- `src/Domain/Exception`: Klasy wyjątków.
- `tests/Domain`: Testy jednostkowe dla agregatów, encji itd.
- `tests/Domain/ValueObject`: Testy jednostkowe dla klas ValueObject.
- 
## Środowisko

1. Zbudowanie środowiska lokalnego w Dockerze
   ```bash
   docker-compose up --build

2. Zainicjowanie Composera
   ```bash
   docker compose run -it ifx-php composer install

3. Uruchamianie testów jednostkowych
   ```bash
   docker compose run -it ifx-php ./vendor/bin/phpunit
