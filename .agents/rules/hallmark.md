# Hallmark Design Rules & Quality Gates (Anti-Slop Framework)

Aquest document estableix les regles de disseny de **Hallmark** per evitar patrons d'interfície d'usuari genèrics o de "IA slop".

## 1. Quality Gates (Filtres de Qualitat de Disseny)
1. **No Hero Centrats Genèrics**: Evitar el típic banner superior amb text centrat i botons de degradat idèntics.
2. **Estructura Visual Variada (Macrostructures)**: Adaptar la disposició del layout segons el propòsit de la pantalla (ex: asimètric, panell de control lateral, cards dinàmiques de diferents mides).
3. **Colors Harmònics en comptes de RGB Purs**: Utilitzar paletes amb tons refinats (HSL / OKLCH, fons foscos elegants o clar refinat amb contrast equilibrat).
4. **Tipografia de Qualitat**: Utilitzar fonts tipus Inter, Roboto o Outfit amb diferenciació clara de jerarquia (`font-weight` i `letter-spacing`).
5. **Micro-Animacions i Feedbacks**: Afegir efectes de transició ràpids i subtils en hover, focus i accions (botons, cards, desplegables).
6. **No Placeholders Genèrics**: Utilitzar dades representatives o imatges generades ad-hoc.

## 2. Modes d'Acció de Hallmark
- **Build**: Crear noves interfícies aplicant jerarquia visual clara i layout diferenciat.
- **Audit**: Avaluar el codi UI existent per detectar i corregir patrons repetitius.
- **Redesign**: Reformular una interfície per donar-li una personalitat visual única mantenint la funcionalitat.
- **Study**: Extreure el "DNA" visual d'un disseny de referència.
