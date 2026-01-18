import React, { lazy, Suspense } from 'react';

const LazyHearingsPage = lazy(() => import('./HearingsPage'));

const HearingsPage = props => (
  <Suspense fallback={null}>
    <LazyHearingsPage {...props} />
  </Suspense>
);

export default HearingsPage;
