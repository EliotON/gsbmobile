import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, Button, Alert, StyleSheet, ActivityIndicator, Switch, TouchableOpacity } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DateTimePicker from '@react-native-community/datetimepicker';

import { StackNavigationProp } from '@react-navigation/stack';

type RootStackParamList = {
  AddVisitPage: undefined;
};

type AddVisitPageNavigationProp = StackNavigationProp<RootStackParamList, 'AddVisitPage'>;

// Ajouter une fonction pour décoder le JWT sans jwtdecode
function decodeJWT(token: string) {
  const base64Url = token.split('.')[1];
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split('')
      .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
      .join('')
  );
  return JSON.parse(jsonPayload);
}

export default function AddVisitPage({ navigation }: { navigation: AddVisitPageNavigationProp }) {
  const [medecins, setMedecins] = useState<{ id_medecin: string; nom: string; prenom: string }[]>([]);
  const [selectedMedecin, setSelectedMedecin] = useState('1'); // Default value
  const [dateVisite, setDateVisite] = useState('2025-01-01'); // Default value
  const [showDatePicker, setShowDatePicker] = useState(false); // New state
  const [heureArrivee, setHeureArrivee] = useState('09:00:00'); // Default value
  const [heureDebutEntretien, setHeureDebutEntretien] = useState('09:15:00'); // Default value
  const [tempsAttente, setTempsAttente] = useState('15'); // Default value
  const [heureDepart, setHeureDepart] = useState('10:00:00'); // Default value
  const [tempsVisite, setTempsVisite] = useState('45'); // Default value
  const [rendezVous, setRendezVous] = useState(false); // Default value
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchMedecins = async () => {
      try {
        const token = await AsyncStorage.getItem('authToken');
        const response = await fetch('https://s5-4242.nuage-peda.fr/gsbapieliot/API/ApiMedecin.php', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setMedecins(data);
      } catch (error) {
        Alert.alert('Erreur', 'Impossible de récupérer la liste des médecins');
      } finally {
        setLoading(false);
      }
    };

    fetchMedecins();
  }, []);

  const handleAddVisit = async () => {
    if (!selectedMedecin || selectedMedecin === '') {
      Alert.alert('Erreur', 'Veuillez sélectionner un médecin valide.');
      return;
    }

    if (!heureArrivee || heureArrivee.trim() === '') {
      Alert.alert('Erreur', 'Veuillez renseigner l\'heure d\'arrivée.');
      return;
    }

    if (!heureDebutEntretien || heureDebutEntretien.trim() === '') {
      Alert.alert('Erreur', 'Veuillez renseigner l\'heure de début d\'entretien.');
      return;
    }

    if (!heureDepart || heureDepart.trim() === '') {
      Alert.alert('Erreur', 'Veuillez renseigner l\'heure de départ.');
      return;
    }

    try {
      const token = await AsyncStorage.getItem('authToken');
      // Décoder le JWT pour extraire l'id_visiteur (adapté selon la structure du token)
      const decodedToken = decodeJWT(token || '');
      const visitorId = decodedToken.userId; // Assurez-vous que 'id' correspond bien à la propriété souhaitée
      const response = await fetch('https://s5-4242.nuage-peda.fr/gsbapieliot/API/ApiVisite.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          id_visiteur: visitorId, // Utilisation de l'id décodé
          id_medecin: selectedMedecin,
          date_visite: dateVisite,
          heure_arrivee: `${dateVisite} ${heureArrivee}`,
          heure_debut_entretien: `${dateVisite} ${heureDebutEntretien}`,
          temps_attente: tempsAttente,
          heure_depart: `${dateVisite} ${heureDepart}`,
          temps_visite: tempsVisite,
          rendez_vous: rendezVous ? 1 : 0, // Convert boolean to integer
        }),
      });

      const data = await response.json();

      if (response.ok) {
        Alert.alert('Succès', 'Visite ajoutée avec succès');
        navigation.goBack(); // Go back to the previous screen
      } else {
        Alert.alert('Erreur', data.error || 'Impossible d\'ajouter la visite');
      }
    } catch (error) {
      Alert.alert('Erreur', 'Une erreur est survenue lors de l\'ajout de la visite');
    }
  };

  if (loading) {
    return (
      <View style={styles.container}>
        <ActivityIndicator size="large" color="#0000ff" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={{ paddingVertical: 16 }}>
        <Text style={{ fontSize: 28, fontWeight: 'bold', color: '#1f2937' }}>
          Ajouter une visite:
        </Text>
      </View>
      <Text style={styles.label}>Médecin</Text>
      <Picker
        selectedValue={selectedMedecin}
        onValueChange={(itemValue: string) => setSelectedMedecin(String(itemValue))}
        style={styles.picker}
      >
        <Picker.Item label="Sélectionnez un médecin" value="" />
        {medecins.map((medecin) => (
          <Picker.Item
            key={medecin.id_medecin}
            label={`${medecin.nom} ${medecin.prenom}`}
            value={String(medecin.id_medecin)}  // Force the value to be a string
          />
        ))}
      </Picker>
      <TouchableOpacity style={styles.input} onPress={() => setShowDatePicker(true)}>
        <Text style={{ color: dateVisite ? '#111827' : '#9CA3AF' }}>
          {dateVisite || "Sélectionnez la date (YYYY-MM-DD)"}
        </Text>
      </TouchableOpacity>
      {showDatePicker && (
        <DateTimePicker
          value={new Date(dateVisite)}
          mode="date"
          display="default"
          onChange={(event, selectedDate) => {
            setShowDatePicker(false);
            if (selectedDate) {
              const formatted = selectedDate.toISOString().split('T')[0];
              setDateVisite(formatted);
            }
          }}
        />
      )}
      <TextInput
        style={styles.input}
        placeholder="Heure d'arrivée (HH:MM:SS)"
        value={heureArrivee}
        onChangeText={setHeureArrivee}
      />
      <TextInput
        style={styles.input}
        placeholder="Heure début entretien (HH:MM:SS)"
        value={heureDebutEntretien}
        onChangeText={setHeureDebutEntretien}
      />
      <TextInput
        style={styles.input}
        placeholder="Temps d'attente (en minutes)"
        value={tempsAttente}
        onChangeText={setTempsAttente}
        keyboardType="numeric"
      />
      <TextInput
        style={styles.input}
        placeholder="Heure de départ (HH:MM:SS)"
        value={heureDepart}
        onChangeText={setHeureDepart}
      />
      <TextInput
        style={styles.input}
        placeholder="Temps de visite (en minutes)"
        value={tempsVisite}
        onChangeText={setTempsVisite}
        keyboardType="numeric"
      />
      <View style={styles.switchContainer}>
        <Text style={styles.label}>Rendez-vous</Text>
        <Switch
          value={rendezVous}
          onValueChange={setRendezVous}
        />
      </View>
      <Button title="Ajouter" onPress={handleAddVisit} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    marginBottom: 8,
  },
  picker: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    marginBottom: 16,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    padding: 8,
    marginBottom: 16,
  },
  switchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
});
