import pandas as pd
from prophet import Prophet
from sklearn.metrics import mean_absolute_error, mean_squared_error
import json
import sys
import os
import logging

# Konfigurasi logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)


def run_forecast(data_path, output_forecast_path, output_metrics_path, forecast_months=12):
    """
    Melakukan forecasting menggunakan Prophet dan menyimpan hasil serta metrik.
    
    Args:
        data_path (str): Path ke file CSV data historis (kolom 'ds', 'y').
        output_forecast_path (str): Path untuk menyimpan hasil forecast (CSV).
        output_metrics_path (str): Path untuk menyimpan metrik evaluasi (JSON).
        forecast_months (int): Jumlah bulan ke depan untuk forecasting.
    """
    try:
        logging.info(f"Memuat data dari {data_path}")
        df = pd.read_csv(data_path)

        # Pastikan kolom ds adalah datetime dan y adalah numerik
        df['ds'] = pd.to_datetime(df['ds'])
        df['y'] = pd.to_numeric(df['y'])

        # Urutkan berdasarkan tanggal
        df = df.sort_values(by='ds').reset_index(drop=True)

        if df.empty:
            logging.error("Tidak ada data historis yang cukup untuk melatih model.")
            return {"status": "error", "message": "Tidak ada data historis."}

        # Split data menjadi latih dan uji (80:20)
        if len(df) < 5:
            logging.warning("Data historis terlalu sedikit untuk pembagian latih/uji. Menggunakan semua data.")
            train_df = df
            test_df = pd.DataFrame()
        else:
            train_size = int(len(df) * 0.8)
            train_df = df.iloc[:train_size]
            test_df = df.iloc[train_size:]

        # Inisialisasi dan latih model Prophet
        logging.info("Melatih model Prophet...")
        model = Prophet(
            changepoint_prior_scale=0.1,
            yearly_seasonality=True,
            weekly_seasonality=False,
            daily_seasonality=False
        )
        model.fit(train_df)
        logging.info("Model Prophet berhasil dilatih.")

        # Prediksi ke depan
        future = model.make_future_dataframe(periods=forecast_months, freq='MS')
        forecast = model.predict(future)

        # Gabungkan nilai aktual
        forecast_with_actual = forecast.set_index('ds')[
            ['yhat', 'yhat_lower', 'yhat_upper']
        ].join(df.set_index('ds')['y']).reset_index()

        # Evaluasi jika ada data uji
        metrics = {}
        if not test_df.empty:
            y_true = test_df['y'].values
            forecast_test = forecast_with_actual[
                forecast_with_actual['ds'].isin(test_df['ds'])
            ]
            y_pred = forecast_test['yhat'].values

            if len(y_true) > 0 and len(y_pred) > 0:
                mae = mean_absolute_error(y_true, y_pred)
                mse = mean_squared_error(y_true, y_pred)
                rmse = mse ** 0.5

                avg_actual = df['y'].mean()
                mae_percent = (mae / avg_actual) * 100 if avg_actual != 0 else 0
                rmse_percent = (rmse / avg_actual) * 100 if avg_actual != 0 else 0

                metrics = {
                    "mae": mae,
                    "mse": mse,
                    "rmse": rmse,
                    "avg_actual_value": avg_actual,
                    "mae_percent": mae_percent,
                    "rmse_percent": rmse_percent,
                    "evaluation_period_start": str(test_df['ds'].min()),
                    "evaluation_period_end": str(test_df['ds'].max())
                }
                logging.info(f"Metrik Model: {metrics}")
            else:
                logging.warning("Tidak ada kecocokan tanggal antara data uji dan forecast.")
                metrics = {"message": "Tidak ada data yang cukup untuk evaluasi."}
        else:
            logging.warning("Tidak tersedia data uji untuk evaluasi.")
            metrics = {"message": "Tidak tersedia data uji untuk evaluasi."}

        # Simpan hasil forecast
        forecast_with_actual[[
            'ds', 'yhat', 'yhat_lower', 'yhat_upper', 'y'
        ]].to_csv(output_forecast_path, index=False)
        logging.info(f"Hasil forecast disimpan ke {output_forecast_path}")

        # Simpan metrik ke file JSON
        with open(output_metrics_path, 'w') as f:
            json.dump(metrics, f, indent=4)
        logging.info(f"Metrik disimpan ke {output_metrics_path}")

        return {
            "status": "success",
            "message": "Forecasting selesai.",
            "metrics": metrics
        }

    except Exception as e:
        logging.error(f"Terjadi kesalahan selama forecasting: {e}", exc_info=True)
        return {"status": "error", "message": str(e)}


if __name__ == "__main__":
    if len(sys.argv) < 4:
        logging.error(
            "Penggunaan: python prophet_forecast.py <data_input_csv> <forecast_output_csv> <metrics_output_json> [forecast_months]"
        )
        sys.exit(1)

    data_input_csv = sys.argv[1]
    forecast_output_csv = sys.argv[2]
    metrics_output_json = sys.argv[3]
    forecast_months = int(sys.argv[4]) if len(sys.argv) > 4 else 12

    result = run_forecast(
        data_input_csv,
        forecast_output_csv,
        metrics_output_json,
        forecast_months
    )

    print(json.dumps(result))