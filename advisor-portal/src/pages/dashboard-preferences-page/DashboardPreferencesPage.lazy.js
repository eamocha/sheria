import React, { lazy, Suspense } from 'react';

const LazyDashboardPreferencesPage = lazy(() => import('./DashboardPreferencesPage'));

const DashboardPreferencesPage = props => (
  <Suspense fallback={null}>
    <LazyDashboardPreferencesPage {...props} />
  </Suspense>
);

export default DashboardPreferencesPage;
