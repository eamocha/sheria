import React, { lazy, Suspense } from 'react';

const LazyTableRowDetailsItem = lazy(() => import('./TableRowDetailsItem'));

const TableRowDetailsItem = props => (
  <Suspense fallback={null}>
    <LazyTableRowDetailsItem {...props} />
  </Suspense>
);

export default TableRowDetailsItem;
