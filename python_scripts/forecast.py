import pandas as pd
from prophet import Prophet
import sys
import os

# Ambil path CSV dari Laravel
csv_path = sys.argv[1]

# Load data
df = pd.read_csv(csv_path)
df['ds'] = pd.to_datetime(df['ds'])
df['y'] = df['y'].astype(float)

model = Prophet(
    changepoint_prior_scale=0.3,
    seasonality_prior_scale=60.0,
    weekly_seasonality=False,
    yearly_seasonality=True
)

# Fit dan forecast
model.fit(df)
future = model.make_future_dataframe(periods=52, freq='W')  # 52 minggu ke depan
forecast = model.predict(future)

# Simpan hasil forecast
# Gunakan path absolut untuk memastikan file tersimpan di tempat yang benar
storage_path = os.path.join(os.path.dirname(csv_path), 'forecast_result.csv')
forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].to_csv(storage_path, index=False)