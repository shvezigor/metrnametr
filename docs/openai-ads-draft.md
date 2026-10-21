# ChatGPT Ads draft for Metr na Metr

Status: preparation only. Do not publish or enable campaigns without explicit approval.

## Account setup

- Advertiser: Metr na Metr
- Website: https://metrnametr.com.ua/
- Store country: Ukraine
- Product feed URL: https://metrnametr.com.ua/feeds/openai-products.csv
- Primary conversion: successful order form (`generate_lead`)
- Secondary conversion: phone click (`phone_click`)
- Checkout in ChatGPT: disabled

## Initial test settings

- Objective: Clicks for the first test; move to Conversions after conversion measurement is verified in Ads Manager.
- Billing currency: select the currency offered to the Ukrainian billing profile.
- Daily budget: minimum allowed by Ads Manager, proposed USD 25 equivalent.
- Duration: 14 days only after explicit launch approval.
- Geography: Ukraine. Use country-level targeting for product-feed campaigns.
- Language: Ukrainian.

## Campaign 1: Entrance doors in Lutsk

Landing page:
https://metrnametr.com.ua/vkhidni-dveri-lutsk?utm_source=chatgpt&utm_medium=cpc&utm_campaign=entrance_doors_lutsk

Context hints:

- купити вхідні двері в Луцьку
- вхідні двері для квартири у Луцьку
- металеві двері з монтажем
- заміна вхідних дверей у квартирі
- двері з доставкою та встановленням у Волинській області

Headline variants:

- Вхідні двері в Луцьку з підбором і монтажем
- Двері для квартири: ціни, моделі та консультація
- Металеві двері у Луцьку від Метр на Метр

Description variants:

- Підберемо модель під розмір отвору, бюджет, вимоги до замків і шумоізоляції. Доступні ціни та характеристики моделей.
- Каталог вхідних дверей для квартир і будинків. Консультація, замір, доставка та монтаж у Луцьку й області.
- Порівняйте комплектації та отримайте консультацію перед замовленням. Вартість монтажу уточнюється після заміру.

## Campaign 2: Doors for a private house

Landing page:
https://metrnametr.com.ua/dveri-dlya-budynku?utm_source=chatgpt&utm_medium=cpc&utm_campaign=doors_for_house

Context hints:

- які двері вибрати для приватного будинку
- вхідні двері для вулиці
- двері з терморозривом
- двері які не промерзають взимку
- утеплені металеві двері для будинку

Headline variants:

- Вхідні двері для приватного будинку
- Двері з терморозривом для холодного входу
- Підбір утеплених дверей для будинку

Description variants:

- Порівняйте утеплення, покриття, замки та конструкцію дверей для зовнішнього встановлення. Допоможемо підібрати модель.
- Двері для будинку з урахуванням холоду, вологи й перепадів температур. Консультація та монтаж у Луцьку й області.
- Пояснимо, коли потрібен терморозрив і які характеристики важливі саме для вашого входу.

## Campaign 3: Interior doors

Landing page:
https://metrnametr.com.ua/mizhkimnatni-dveri-lutsk?utm_source=chatgpt&utm_medium=cpc&utm_campaign=interior_doors_lutsk

Context hints:

- купити міжкімнатні двері в Луцьку
- міжкімнатні двері з монтажем
- двері для ремонту квартири
- підбір кольору і покриття міжкімнатних дверей

Headline variants:

- Міжкімнатні двері в Луцьку
- Підбір міжкімнатних дверей для квартири
- Міжкімнатні двері з консультацією та монтажем

Description variants:

- Каталог моделей із цінами, покриттями та варіантами оформлення. Допоможемо підібрати двері під інтер'єр.
- Підбір полотна, кольору, фурнітури й розміру. Уточніть комплектацію та монтаж перед замовленням.
- Міжкімнатні двері для квартири або будинку з доставкою та встановленням у Луцьку.

## Product-feed campaign

Create only after Ads Manager successfully validates the feed URL. Keep the campaign paused. Split products by confirmed category or product metadata only; do not infer availability, brand, ratings, reviews, or warranty terms.

## Pre-launch checklist

- Ads Manager account and advertiser identity approved.
- Domain approved.
- Feed upload has no rejected rows.
- OAI-AdsBot and OAI-SearchBot can access landing pages and images.
- `generate_lead` and `phone_click` are visible in conversion measurement.
- Landing-page URLs and UTM parameters were tested.
- Every campaign and ad group remains Draft or Paused.
- Billing and final daily budget are reviewed by the owner.
- Explicit launch approval is received.
