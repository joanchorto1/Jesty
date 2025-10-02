# Dashboard CRM – Componentes visuales e interacciones

- **Encabezado hero**: bloque con degradado `from-violet-700 via-blue-700 to-slate-900`, títulos y descripción introductoria.
- **Tarjetas KPI**: cuatro tarjetas con borde translúcido y blur que muestran métricas (`Leads`, `Oportunidades`, `Notas`, `Actividades`). Incluyen valores, subtítulos y utilizan `computed` para totals.
- **Gráfico de líneas**: sección "Actividad mensual" envuelta en tarjeta blanca (`rounded-3xl shadow-xl`) que renderiza `<LineChart>` con datos agregados de actividades.
- **Próximas acciones**: lista desplazable (`max-h-80`) con actividades futuras, cada elemento es tarjeta con borde suave y punto de estado.
- **Gráficos secundarios**: tres tarjetas blancas con `<PieChart>`, `<BarChart>` y `<PolarChart>` mostrando origen de leads, conversión de oportunidades y embudo respectivamente.
- **Estados vacíos**: lista "Próximas acciones" muestra mensaje "No hay actividades programadas" cuando no hay datos.
- **Interacciones**:
  - Computados para totales, tasas y embudo (`conversionRate`, `salesFunnelChart`).
  - Ordenamientos y filtrado para `upcomingActivities` y agregaciones mensuales.
  - Formateo de fechas a través de `formatDate` helper.
