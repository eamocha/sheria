import React, { lazy, Suspense } from 'react';

const LazyHearingsTableRowMenu = lazy(() => import('./HearingsTableRowMenu'));

const HearingsTableRowMenu = props => (
  <Suspense fallback={null}>
    <LazyHearingsTableRowMenu {...props} />
  </Suspense>
);

export default HearingsTableRowMenu;
